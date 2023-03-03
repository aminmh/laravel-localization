<?php

namespace Bugloos\LaravelLocalization;

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
    )
    {
        parent::__construct($loader, $locale);
        $this->namespaceResolver = new NamespacedItemResolver();
    }

    public function get($key, array $replace = [], $locale = null, $fallback = true): string
    {
        $locale = $locale ?: $this->getLocale();

        if (false !== preg_match('/[a-zA-Z]+::(\w+\.)*|[a-zA-Z]+(\w*\.)*/', $key)) {
            // The key is not in JSON translation files

            [$namespace, $group, $item] = $this->namespaceResolver->parseKey($key);

            foreach ($this->getLocaleOrFallback($locale, $fallback) as $localeOrFallback) {
                if (!is_null($line = $this->getLine($namespace, $group, $localeOrFallback, $item, $replace))) {
                    return $line;
                }
            }
        }

        //TODO Load keys that are pointed to JSON file

        return $this->makeReplacements($line ?: $key, $replace);
    }

    public function addLabel($key, $category): Label
    {
        if (!$category instanceof Category) {
            $category = Category::findBy($category) ?? throw new ModelNotFoundException(sprintf("The category %s not found!", $category), 404);
        }

        $label = new Label([
            'key' => $key,
        ]);

        $label->category()->associate($category);

        $label->save();

        return $label;
    }

    public function addCategory(string $name): Category
    {
        return Category::create([
            'name' => $name,
        ]);
    }

    public function translate(Label $label, string $translation, ?string $locale = null): Translation
    {
        if ($locale) {
            $localeObject = $this->findLocale($locale)->first() ?? throw new ModelNotFoundException(sprintf('The %s not found!', $locale), 404);
        } else {
            $localeObject = $this->findLocale($this->getLocale())->first();
        }

        if (false !== ($oldTranslation = $this->hasTranslationWithLocale($label, $localeObject))) {
            return $this->updateTranslation($oldTranslation, $translation);
        }

        return $this->createNewTranslation($label, $translation, $localeObject);
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
                $newTranslationBag[] = Arr::except($translationObject->toArray(), ['label', 'locale']);
            }
        }

        try {
            return Translation::query()->insert($newTranslationBag);
        } catch (QueryException $ex) {
            // TODO: Throw custom exception
        }
    }

    public function translated(?string $locale = null): array|Collection
    {
        $locale = $locale ?: $this->getLocale();

        $isLocaleActive = (bool)$this->findLocale($locale, true)->first();

        if ($isLocaleActive) {
            return Category::with('labels')->get()
                ->map(static function (Category $category) use ($locale) {
                    return [
                        $category->getAttribute('name') => $category->labels()
                            ->with('translation', function (Relation $query) use ($locale) {
                                $query->whereRelation('locale', 'locale', $locale);
                            })
                            ->lazy(100)
                            ->pluck('translation.text', 'key')
                            ->all(),
                    ];
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

        return Category::all()
            ->map(function (Category $category) {
                return $category->setAttribute(
                    'not_translated',
                    $this->findLocale(active: true)
                        ->get()
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

    private function notTranslatedInLocale(string|Language $locale)
    {
        if (is_string($locale)) {
            $locale = $this->findLocale($locale, true);
        }

        return $locale->setAttribute(
            'labels',
            Label::query()->whereHas('notTranslated', static function (Builder $query) use ($locale) {
                $query->whereRelation('locale', 'locale', $locale->getAttribute('locale'));
            })->get()
        );
    }

    private function notTranslatedInCategory($category)
    {
        if (!$category instanceof Category) {
            $category = Category::findBy($category);
        }

        return $category->labels()
            ->whereHas('notTranslated')->get();
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
