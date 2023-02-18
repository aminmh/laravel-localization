<?php

namespace Bugloos\LaravelLocalization\Facades;

use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Models\Label;
use Bugloos\LaravelLocalization\Models\Language;
use Illuminate\Support\Collection;

/**
 * @method static bool has(string $key, ?string $locale = null, ?string $fallback = true)
 * @method static bool addLabel(string $key, int|string|Category $group)
 * @method static Category addCategory(string $name)
 * @method static bool translate(Label $label, string $text, ?string $locale = null)
 * @method static bool bulkTranslate(Label $label, array $translations)
 * @method static array|Collection translated(?string $locale = null)
 * @method static array|Collection notTranslated(string|Language|null $locale = null, $category = null)
 * @method static string flagPath(string $locale)
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
