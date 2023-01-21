<?php

namespace Bugloos\LaravelLocalization;

use Illuminate\Support\NamespacedItemResolver;
use Illuminate\Translation\Translator as BaseTranslator;

class Translator extends BaseTranslator
{
    protected readonly NamespacedItemResolver $namespaceResolver;

    public function __construct(
        protected Loader $loader,
        private string $locale
    ) {
        parent::__construct($loader, $locale);
        $this->namespaceResolver = new NamespacedItemResolver();
    }

    public function get($key, array $replace = [], $locale = null, $fallback = null)
    {
        $locale = $locale ?: $this->getLocale();

        if (false !== preg_match('/[a-zA-Z]+::/',$key)) {
            // The key is not in JSON translation files

            [$namespace,$group,$item] = $this->namespaceResolver->parseKey($key);

            $locales = $fallback ?: [$locale,$fallback];

            foreach ($locales as $locale) {
                $this->loader->loadPhpTranslations($locale, $group, $namespace);
            }
        }
    }
}
