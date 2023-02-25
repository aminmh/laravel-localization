<?php

namespace Bugloos\LaravelLocalization\Migrator\Writers;

use Bugloos\LaravelLocalization\Abstract\AbstractWriter;
use Bugloos\LaravelLocalization\Contracts\LazyPersistsWriteInterface;
use Bugloos\LaravelLocalization\Responses\SuccessMigratorResponse;
use Illuminate\Database\QueryException;

class ArrayWriter extends AbstractWriter implements LazyPersistsWriteInterface
{
    public function save(): \Generator
    {
        $categoryObject = static::$translator->addCategory($category = $this->reader->getCategory());

        $locale = $this->reader->getLocale();

        foreach ($this->reader->getContent() as $label => $translate) {
            try {
                $labelObject = static::$translator->addLabel($label, $categoryObject);

                static::$translator->translate($labelObject, $translate, $locale);

                yield new SuccessMigratorResponse($label, $category, $translate, $locale);
            } catch (QueryException $ex) {
                yield false;
            }
        }

        yield;
    }
}
