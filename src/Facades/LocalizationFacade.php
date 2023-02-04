<?php

namespace Bugloos\LaravelLocalization\Facades;

use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Models\Label;
use Bugloos\LaravelLocalization\Translator;

/**
 * @method static bool has(string $key, ?string $locale = null, ?string $fallback = true)
 * @method static bool addLabel(string $key, int|string|Category $group)
 * @method static Category addCategory(string $name)
 * @method static bool translate(Label|string|int $label, string $text, $category = null, ?string $locale = null)
 * @method static array translated(?string $locale = null)
 *
 * @see \Bugloos\LaravelLocalization\Translator
 */
class LocalizationFacade extends \Illuminate\Support\Facades\Facade
{

    protected static function getFacadeAccessor(): string
    {
        return 'localization';
    }
}
