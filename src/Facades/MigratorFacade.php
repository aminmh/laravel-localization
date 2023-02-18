<?php

namespace Bugloos\LaravelLocalization\Facades;

/**
 * @method static void migrate(string $path)
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
