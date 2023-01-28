<?php

namespace Bugloos\LaravelLocalization;

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

    public function has($key, $locale = null, $fallback = true): bool
    {
        [$namespace, $group, $item] = $this->namespaceResolver->parseKey($key);

        $locales = $this->getLocaleOrFallback($locale, $fallback);

        $localeIds = Language::query()->whereIn('locale', $locales)->get()->pluck('id')->toArray();

        if (is_null($namespace) || $namespace === '*') {
            return (bool)Translation::query()
                ->whereRelation('label', 'key', $item)
                ->whereRelation('label.category', 'name', $group)
                ->whereIn('language_id', $localeIds)
                ->get()
                ->count();
        }

        //TODO Handle translation exists when namespace was passed

        return false;
    }

    public function get($key, array $replace = [], $locale = null, $fallback = null)
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

    protected function getLocaleOrFallback($locale = null, $fallback = null)
    {
        return $fallback ? $this->localeArray($locale) : [$locale];
    }
}
