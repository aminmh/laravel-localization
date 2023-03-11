<?php

namespace Bugloos\LaravelLocalization\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void load(string $path, array $filter = [])
 * @method static bool purge()
 *
 * @see \Bugloos\LaravelLocalization\Migrator\Migrator
 */
class MigratorFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'localization.migrator';
    }
}
