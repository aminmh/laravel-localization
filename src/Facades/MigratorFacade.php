<?php

namespace Bugloos\LaravelLocalization\Facades;

/**
 * @method static void migrate(string $path)
 * @method static array convertNestedJson2FlatArray(string $path)
 * @method static bool normalizeFlatArray2Associate(array $normalizedData, string $locale)
 *
 * @see \Bugloos\LaravelLocalization\Migrator\Migrator
 */
class MigratorFacade extends \Illuminate\Support\Facades\Facade
{

    protected static function getFacadeAccessor(): string
    {
        return 'localization.migrator';
    }
}
