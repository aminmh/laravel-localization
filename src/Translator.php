<?php

namespace Bugloos\LaravelLocalization;

use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Models\Label;
use Bugloos\LaravelLocalization\Models\Language;
use Bugloos\LaravelLocalization\Models\Translation;
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
            $group = Category::query()->find($group);
        }

        if (is_string($group)) {

            if (!$this->isGroupExists($group)) {
                $group = $this->addCategory($group);
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

    protected function getLocaleOrFallback($locale = null, $fallback = null): array
    {
        return $fallback ? $this->localeArray($locale) : [$locale];
    }

    private function isGroupExists(string $name): bool
    {
        return Category::query()->where('name', $name)->exists();
    }
}
