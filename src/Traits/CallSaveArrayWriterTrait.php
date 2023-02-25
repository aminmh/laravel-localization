<?php

namespace Bugloos\LaravelLocalization\Traits;

use Bugloos\LaravelLocalization\Migrator\Writers\ArrayWriter;

trait CallSaveArrayWriterTrait
{
    public function skipOnFailSave(ArrayWriter $writer): void
    {
        foreach ($writer->save() as $result) {
            echo $result;
        }
    }

    public function saveWithCallback(ArrayWriter $writer, \Closure $callback): void
    {
        foreach ($writer->save() as $result) {
            $callback($result);
        }
    }
}
