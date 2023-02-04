<?php

namespace Bugloos\LaravelLocalization;

use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Models\Label;
use Bugloos\LaravelLocalization\Models\Language;
use Bugloos\LaravelLocalization\Models\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            $locale = $this->findLocale($locale) ?? throw new ModelNotFoundException(sprintf("The %s not found!", $locale), 404);
        } else {
            $locale = $this->findLocale($this->getLocale());
        }

        if (false !== ($oldTranslation = $this->hasTranslationWithLocale($label, $locale))) {
            return $this->updateTranslation($oldTranslation, $translation);
        }

        return $this->createNewTranslation($label, $translation, $locale);

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

    private function findLocale(string $locale): Builder|Language|null
    {
        return Language::query()
            ->where('locale', $locale)
            ->orWhere('name')
            ->first();
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
