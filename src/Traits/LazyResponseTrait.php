<?php

namespace Bugloos\LaravelLocalization\Traits;

use Bugloos\LaravelLocalization\Abstract\AbstractMigratorResponse as MigratorResponse;
use Bugloos\LaravelLocalization\Migrator\Writers\ArrayWriter;

trait LazyResponseTrait
{
    public function iterateResponse(ArrayWriter $writer, callable $successCallback, callable $failedCallback)
    {
        foreach ($writer->save() as $result) {
            if ($result->isStatusOk()) {
                $successCallback($result);
            } else {
                $failedCallback($result);
            }
        }
    }

    /**
     * @param ArrayWriter $writer
     * @param callable(MigratorResponse):void $callback
     * @return void
     */
    public function skipOnFailResponse(ArrayWriter $writer, callable $callback): void
    {
        /** @var MigratorResponse $result */
        foreach ($writer->save() as $result) {
            if ($result->isStatusOk()) {
                $callback($result);
            }
        }
    }

    public function saveFailedResponse(ArrayWriter $writer, callable $callback): void
    {
        foreach ($writer->save() as $result) {
            $callback($result);
        }
    }
}
