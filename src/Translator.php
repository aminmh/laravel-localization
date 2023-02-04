<?php

namespace Bugloos\LaravelLocalization;

use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Models\Label;
use Bugloos\LaravelLocalization\Models\Language;
use Bugloos\LaravelLocalization\Models\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
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

    public function get($key, array $replace = [], $locale = null, $fallback = null): string
    {
        $locale = $locale ?: $this->getLocale();

        if (false !== preg_match('/[a-zA-Z]+::(\w+\.)*|[a-zA-Z]+(\w*\.)*/', $key)) {
            // The key is not in JSON translation files

            [$namespace, $group, $item] = $this->namespaceResolver->parseKey($key);

            $locales = $this->getLocaleOrFallback($locale, $fallback);

            foreach ($locales as $locale) {
                if (!is_null($line = $this->getLine($namespace, $group, $locale, $item, $replace))) {
                    return $line;
                }
            }
        }

        //TODO Load keys that are pointed to JSON file

        return $this->makeReplacements($line ?: $key, $replace);
    }

    public function addLabel($key, $group): bool
    {
        if (is_numeric($group)) {
            $group = $this->getCategory($group);
        }

        if (is_string($group)) {

            if (!$this->isGroupExists($group)) {
                $group = $this->addCategory($group);
            } else {
                $group = $this->getCategory($group);
            }
        }

        $label = new Label([
            'key' => $key
        ]);

        $label->category()->associate($group);

        return $label->save();
    }

    public function addCategory(string $name): Category
    {
        return Category::create([
            'name' => $name
        ]);
    }

    public function translate($label, string $translation, $category = null, ?string $locale = null): bool
    {
        if (!$label instanceof Label) {
            $label = $this->getLabel($label, $category);
        }

        if ($locale) {
            $locale = $this->findLocale($locale)->first() ?? throw new ModelNotFoundException(sprintf("The %s not found!", $locale), 404);
        } else {
            $locale = $this->findLocale($this->getLocale())->first();
        }

        if (false !== ($oldTranslation = $this->hasTranslationWithLocale($label, $locale))) {
            return $this->updateTranslation($oldTranslation, $translation);
        }

        return $this->createNewTranslation($label, $translation, $locale);

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
                            ->all()
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

    public function notTranslatedInLocale(string|Language $locale)
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

    public function notTranslatedInCategory($category)
    {
        if (!$category instanceof Category) {
            $category = $this->getCategory($category);
        }

        return $category->labels()
            ->whereHas('notTranslated')->get();
    }

    protected function getLocaleOrFallback($locale = null, $fallback = null): array
    {
        return $fallback ? $this->localeArray($locale) : [$locale];
    }

    private function updateTranslation(Translation $oldTranslation, string $newTranslationText): bool
    {
        $oldTranslation->setAttribute('text', $newTranslationText);
        return $oldTranslation->save();
    }

    private function createNewTranslation($label, string $translation, ?Language $locale = null): bool
    {
        $translationModel = new Translation([
            'text' => $translation
        ]);

        $translationModel->label()->associate($label);

        $translationModel->locale()->associate($locale);

        return $translationModel->save();
    }

    private function isGroupExists(string $name): bool
    {
        return $this->getCategory($name)?->exists() ?? false;
    }

    private function getCategory(string|int $identifier): Category|Builder|null
    {
        $categoryQuery = Category::query();

        if (is_numeric($identifier)) {
            return $categoryQuery->find($identifier);
        }

        return $categoryQuery->firstWhere('name', $identifier);
    }

    private function getLabel(string|int $identifier, $category = null): Label|Builder|null
    {
        $labelQuery = Label::query();

        if (is_numeric($identifier)) {
            return $labelQuery->find($identifier);
        } elseif (is_null($category)) {
            throw new \BadMethodCallException("For find label with key, category is required!", 400);
        }

        $category = $this->getCategory($category);

        return $labelQuery
            ->whereRelation('category', 'name', $category->getAttribute('name'))
            ->firstWhere('key', $identifier);
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

        return $query->exists() ? $query->first() : false;
    }
}
