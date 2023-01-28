<?php

namespace Bugloos\LaravelLocalization\Facades;

use Bugloos\LaravelLocalization\Translator;

/**
 * @method static bool has(string $key, ?string $locale = null, ?string $fallback = true)
 *
 * @see \Bugloos\LaravelLocalization\Translator
 */
class LocalizationFacade extends \Illuminate\Support\Facades\Facade
{

    protected static function getFacadeAccessor()
    {
        return 'localization';
    }
}
