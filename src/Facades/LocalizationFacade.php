<?php

namespace Bugloos\LaravelLocalization\Facades;

class LocalizationFacade extends \Illuminate\Support\Facades\Facade
{

    protected static function getFacadeAccessor()
    {
        return 'localization';
    }
}
