<?php

namespace Bugloos\LaravelLocalization\Migrator\MigratorStrategies;

use Bugloos\LaravelLocalization\Abstract\AbstractWriter;
use Bugloos\LaravelLocalization\Exceptions\TranslationFailureException;

class ArrayMigrator extends AbstractWriter
{
    public function migrate(): void
    {
        $categoryObject = static::$translator->addCategory($this->loader->getCategory());

        $locale = $this->loader->getLocale();

        $data = $this->loader->getContent();

        foreach ($data as $label => $translate) {
            try {
                $labelObject = static::$translator->addLabel($label, $categoryObject);

                static::$translator->translate($labelObject, $translate, $locale);
            } catch (TranslationFailureException $ex) {
                echo $ex->getMessage();
                continue;
            }
        }
    }
}
