<?php

namespace Bugloos\LaravelLocalization\Migrator\Writers;

use Bugloos\LaravelLocalization\Abstract\AbstractWriter;
use Bugloos\LaravelLocalization\Migrator\ReaderStrategies\PhpReaderStrategy;
use Bugloos\LaravelLocalization\Views\Console\Console;

class JsonWriter extends AbstractWriter
{
    public function save(): bool
    {
        if (empty($data = $this->reader->getContent())) {
            return false;
        }

        $locale = $this->reader->getLocale();

        try {
            foreach ($data as $category => $labelAndTranslate) {
                $phpReader = new PhpReaderStrategy();
                $phpReader->setCategory($category)
                    ->setLocale($locale)
                    ->setContent($labelAndTranslate);

                (new PhpWriter($phpReader))->save();
            }

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }
}
