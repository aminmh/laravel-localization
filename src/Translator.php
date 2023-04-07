<?php

namespace Bugloos\LaravelLocalization;

use Bugloos\LaravelLocalization\DTO\FailedTranslationDTO;
use Bugloos\LaravelLocalization\Enums\ResourceExceptionMessages as ExceptionMessages;
use Bugloos\LaravelLocalization\Exceptions\LocalizationResourceException;
use Bugloos\LaravelLocalization\Exceptions\TranslationFailureException;
use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Models\Label;
use Bugloos\LaravelLocalization\Models\Language;
use Bugloos\LaravelLocalization\Models\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\NamespacedItemResolver;
use Illuminate\Translation\Translator as BaseTranslator;

class Translator extends BaseTranslator
{
    protected readonly NamespacedItemResolver $namespaceResolver;

    public function __construct(
        Loader $loader,
        string $locale
    ) {
        parent::__construct($loader, $locale);
        $this->namespaceResolver = new NamespacedItemResolver();
    }

    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $locale = $locale ?: $this->locale;

        // For JSON translations, there is only one file per locale, so we will simply load
        // that file, and then we will be ready to check the array for the key. These are
        // only one level deep, so we do not need to do any fancy searching through it.
        $this->load('*', '*', $locale);

        try {
            $line = $this->loaded['*']['*'][$locale][$key];
        } catch (\Exception) {
            $line = Arr::get($this->loaded['*']['*'][$locale], $key);
        }

        // If we can't find a translation for the JSON key, we will attempt to translate it
        // using the typical translation file. This way developers can always just use a
        // helper such as __ instead of having to pick between trans or __ with views.
        if (!isset($line)) {
            [$namespace, $group, $item] = $this->parseKey($key);

            // Here we will get the locale that should be used for the language line. If one
            // was not passed, we will use the default locales which was given to us when
            // the translator was instantiated. Then, we can load the lines and return.
            $locales = $fallback ? $this->localeArray($locale) : [$locale];

            foreach ($locales as $locale) {
                if (!is_null($line = $this->getLine(
                    $namespace,
                    $group,
                    $locale,
                    $item,
                    $replace
                ))) {
                    return $line;
                }
            }
        }

        // If the line doesn't exist, we will return back the key which was requested as
        // that will be quick to spot in the UI if language keys are wrong or missing
        // from the application's language files. Otherwise, we can return the line.
        return $this->makeReplacements($line ?: $key, $replace);
    }

    public function addLabel($key, $category): Label
    {
        if (!$category instanceof Category) {
            $category = Category::findBy($category)
                ?? throw new LocalizationResourceException(ExceptionMessages::NOT_FOUND, code: 404, resources: $category);
        }

        try {
            $label = new Label([
                'key' => $key,
            ]);

            $label->category()->associate($category);

            $label->save();

            return $label;
        } catch (QueryException $ex) {
            throw new LocalizationResourceException(ExceptionMessages::ADD_FAILED, 400, $ex, $key);
        }
    }

    public function addCategory(string $name): Category
    {
        try {
            return Category::create([
                'name' => $name,
            ]);
        } catch (QueryException $ex) {
            throw new LocalizationResourceException(ExceptionMessages::ADD_FAILED, 400, $ex, $name);
        }
    }

    public function translate(Label $label, string $translation, ?string $locale = null): Translation
    {
        try {
            if ($locale) {
                $localeObject = $this->findLocale($locale)->first() ?? throw new ModelNotFoundException(sprintf(ExceptionMessages::NOT_FOUND->value, $locale), 404);
            } else {
                $localeObject = $this->findLocale($this->getLocale())->first();
            }

            if (false !== ($oldTranslation = $this->hasTranslationWithLocale($label, $localeObject))) {
                return $this->updateTranslation($oldTranslation, $translation);
            }

            return $this->createNewTranslation($label, $translation, $localeObject);
        } catch (QueryException $ex) {
            throw new TranslationFailureException(
                new FailedTranslationDTO($label->key, $label->category->name, $translation, $locale),
                $ex
            );
        } catch (ModelNotFoundException $ex) {
            throw new LocalizationResourceException($ex->getMessage(), 400, $ex, $locale);
        }
    }

    public function bulkTranslate(Label $label, array $translations): bool
    {
        /** @var Collection $locales */
        $locales = Language::query()->scopes('actives')
            ->whereIn('locale', array_keys($translations))
            ->get()
            ->flatMap(static function (Language $locale) {
                return [$locale->getAttribute('locale') => $locale];
            });

        $newTranslationBag = [];

        foreach ($translations as $locale => $translation) {
            if ($locales->has($locale)) {
                $translationObject = new Translation([
                    'text' => $translation
                ]);

                $translationObject->locale()->associate($locales[$locale]);
                $translationObject->label()->associate($label);
                $newTranslationBag[] = array_intersect_key($translationObject->toArray(), array_flip(['label_id', 'language_id', 'text']));
            }
        }

        try {
            return Translation::query()->insert($newTranslationBag);
        } catch (QueryException $ex) {
            throw new LocalizationResourceException(ExceptionMessages::BULK_TRANSLATION, 400, previous: $ex);
        }
    }

    public function translated(?string $locale = null): array|Collection
    {
        $locale = $locale ?: $this->getLocale();

        $isLocaleActive = (bool)$this->findLocale($locale, true)->first();

        if ($isLocaleActive) {
            return Category::with('labels')->get()
                ->map(static function (Category $category) use ($locale) {
                    return $category->setAttribute(
                        'translated_labels',
                        $category->labels()
                            ->with('translation', function (Relation $query) use ($locale) {
                                $query->whereRelation('locale', 'locale', $locale);
                            })
                            ->lazy(100)
                            ->pluck('translation.text', 'key')
                            ->all()
                    );
                });
        }

        return [];
    }

    public function notTranslated($locale = null, $category = null)
    {
        if ($locale) {
            return $this->notTranslatedInLocale($locale);
        }

        if ($category) {
            return $this->notTranslatedInCategory($category);
        }

        $activeLocales = $this->findLocale(active: true)->get();

        return Category::all()
            ->map(function (Category $category) use ($activeLocales) {
                return $category->setAttribute(
                    'not_translated',
                    $activeLocales
                        ->map(function (Language $locale) use ($category) {
                            return $locale->setAttribute(
                                'labels',
                                $category->labels()
                                    ->whereHas('notTranslated', static function (Builder $query) use ($locale) {
                                        $query->whereRelation('locale', 'locale', $locale->getAttribute('locale'));
                                    })
                                    ->get()
                            );
                        })
                );
            });
    }

    public function flagPath(string $locale): string
    {
        if (str_contains($locale, '_')) {
            $locale = explode('_', $locale)[0]; // en => en
        }

        $path = config('localization.flag.path') ?? realpath(Language::DEFAULT_FLAG_PATH);

        $mimeType = config('localization.flag.mime_type') ?? 'png';

        if (str_ends_with($path, '/')) {
            $path = substr($path, 0, strrpos($path, '/') - 1);
        }

        return ($path .= "/{$locale}.{$mimeType}");
    }

    public function activeLanguage(string $locale): bool
    {
        /** @var Language $language */
        $language = $this->findLocale($locale, false)->first();
        return $language?->activationToggle(true) ?? false;
    }

    public function deActiveLanguage(string $locale): bool
    {
        /** @var Language $language */
        $language = $this->findLocale($locale, true)->first();
        return $language?->activationToggle(false) ?? false;
    }

    private function notTranslatedInLocale(string|Language $locale): Language
    {
        if (is_string($locale)) {
            $locale = $this->findLocale($locale, true)->first();
        }

        return $locale->setAttribute(
            'labels',
            Label::query()->whereHas('notTranslated', static function (Builder $query) use ($locale) {
                $query->whereRelation('locale', 'locale', $locale->getAttribute('locale'));
            })
                ->union(
                    Label::query()->whereDoesntHave('translations', static function (Builder $query) use ($locale) {
                        $query->whereRelation('locale', 'locale', $locale->getAttribute('locale'));
                    })
                )
                ->get()
        );
    }

    private function notTranslatedInCategory($category)
    {
        if (!$category instanceof Category) {
            $category = Category::findBy($category);
        }

        return $category->labels()
            ->whereHas('notTranslated')
            ->union($category->labels()->getQuery()->whereDoesntHave('translations'))
            ->get();
    }

    protected function getLocaleOrFallback($locale, bool $fallback): array
    {
        return $fallback ? $this->localeArray($locale) : [$locale];
    }

    private function updateTranslation(Translation $oldTranslation, string $newTranslationText): Translation
    {
        $oldTranslation->setAttribute('text', $newTranslationText);

        $oldTranslation->save();

        return $oldTranslation;
    }

    private function createNewTranslation($label, string $translation, ?Language $locale = null): Translation
    {
        $translationModel = new Translation([
            'text' => $translation,
        ]);

        $translationModel->label()->associate($label);

        $translationModel->locale()->associate($locale);

        $translationModel->save();

        return $translationModel;
    }

    private function findLocale(?string $locale = null, ?bool $active = null): Builder
    {
        $query = Language::query();

        if ($locale) {
            $query->where('locale', $locale);
        }

        if ($active) {
            return $query->where('active', true);
        }

        if ($active === false) {
            return $query->where('active', false);
        }

        return $query;
    }

    private function hasTranslationWithLocale(Label $label, Language $locale): bool|Translation
    {
        $query = $label->translations()
            ->whereHas(
                relation: 'locale',
                callback: static function (Builder $query) use ($locale) {
                    $query->where('locale', $locale->getAttribute('locale'));
                }
            );

        return $query->first() ?: false;
    }
}
