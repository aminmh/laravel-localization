<?php

namespace Bugloos\LaravelLocalization\Migrator\Writers;

use Bugloos\LaravelLocalization\Abstract\AbstractWriter;
use Bugloos\LaravelLocalization\Migrator\ReaderStrategies\PhpReaderStrategy;

class JsonWriter extends AbstractWriter
{
    public function save(): bool
    {
        if (empty($normalizedData = $this->reader->getContent())) {
            return false;
        }

        $locale = $this->reader->getLocale();

        $data = [];

        try {
            while ($payload = key($normalizedData)) {
                $sections = explode('.', $payload);
                $category = $sections[0];

                if (!array_key_exists($category, $data)) {
                    $data[$category] = [];
                }

                $label = implode('.', array_slice($sections, 1));
                $data[$category][$label] = reset($normalizedData);
                unset($normalizedData[$payload]);
            }

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
