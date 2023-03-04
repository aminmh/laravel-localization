<?php

namespace Bugloos\LaravelLocalization\Migrator\Writers;

use Bugloos\LaravelLocalization\Abstract\AbstractWriter;
use Bugloos\LaravelLocalization\Contracts\LazyPersistsWriteInterface;
use Bugloos\LaravelLocalization\Migrator\ReaderStrategies\ArrayReaderStrategy;
use Bugloos\LaravelLocalization\Traits\LazyResponseTrait;

class JsonWriter extends AbstractWriter implements LazyPersistsWriteInterface
{
    use LazyResponseTrait;

    public function save(): \Generator
    {
        if (empty($data = $this->reader->getContent())) {
            return false;
        }

        $locale = $this->reader->getLocale();

        foreach ($data as $category => $labelAndTranslate) {
            $arrayReader = new ArrayReaderStrategy();

            $arrayReader->setCategory($category)
                ->setLocale($locale)
                ->setContent($labelAndTranslate);

            yield from (new ArrayWriter($arrayReader))->save();
        }
    }
}
