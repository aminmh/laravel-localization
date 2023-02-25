<?php

namespace Bugloos\LaravelLocalization\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Generator lazyLoad(string $path, array $filter = [])
 * @method static array load(string $path, array $filter = [])
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
