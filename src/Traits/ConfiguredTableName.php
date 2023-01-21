<?php

namespace Bugloos\LaravelLocalization\Traits;

trait ConfiguredTableName
{
    public function getTable(string $model = null)
    {
        if ($model) {
            return config('localization.tables')[$model];
        }

        return config('localization.tables')[get_class($this)];
    }
}
