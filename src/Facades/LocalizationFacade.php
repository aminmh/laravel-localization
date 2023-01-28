<?php

namespace Bugloos\LaravelLocalization\Facades;

use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Translator;

/**
 * @method static bool has(string $key, ?string $locale = null, ?string $fallback = true)
 * @method static bool addLabel(string $key, int|string|Category $group)
 * @method static Category addCategory(string $name)
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
