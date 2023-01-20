<?php

namespace Bugloos\LaravelLocalization\Traits;

trait ConfiguredTableName
{
    public function getTableName(string $model)
    {
        return config('localization.tables')[$model];
    }
}
