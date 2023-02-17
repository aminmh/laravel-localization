<?php

use Bugloos\LaravelLocalization\Loader;
use Bugloos\LaravelLocalization\Translator;

if (! function_exists('trans_get')) {
    function translate(string $key, array $replace = [], ?string $locale = null): string
    {
        $loader = new Loader(app('files'), app('path.lang'));

        return
            (new Translator($loader, app()->getLocale()))
                ->get($key, $replace, $locale);
    }
}
