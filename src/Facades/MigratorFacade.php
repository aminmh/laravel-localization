<?php

namespace Bugloos\LaravelLocalization\Facades;

/**
 * @method static void load(string $path, array $filter = [])
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
