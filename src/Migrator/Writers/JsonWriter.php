<?php

namespace Bugloos\LaravelLocalization\Migrator\Writers;

use Bugloos\LaravelLocalization\Abstract\AbstractWriter;
use Bugloos\LaravelLocalization\Contracts\PersistsWriteInterface;
use Bugloos\LaravelLocalization\Migrator\ReaderStrategies\ArrayReaderStrategy;

class JsonWriter extends AbstractWriter implements PersistsWriteInterface
{
    public function save(): bool
    {
        if (empty($data = $this->reader->getContent())) {
            return false;
        }

        $locale = $this->reader->getLocale();

        try {
            foreach ($data as $category => $labelAndTranslate) {
                $arrayReader = new ArrayReaderStrategy();

                $arrayReader->setCategory($category)
                    ->setLocale($locale)
                    ->setContent($labelAndTranslate);

                foreach ((new ArrayWriter($arrayReader))->save() as $item) {
                    if ($item !== false) {
                        echo $item;
                    }
                }
            }

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }
}
