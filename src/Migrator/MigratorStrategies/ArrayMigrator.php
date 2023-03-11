<?php

namespace Bugloos\LaravelLocalization\Migrator\MigratorStrategies;

use Bugloos\LaravelLocalization\Abstract\AbstractWriter;

class ArrayMigrator extends AbstractWriter
{
    public function migrate(): void
    {
        $categoryObject = static::$translator->addCategory($this->loader->getCategory());

        $locale = $this->loader->getLocale();

        $data = $this->loader->getContent();

        foreach ($data as $label => $translate) {
            $labelObject = static::$translator->addLabel($label, $categoryObject);

            static::$translator->translate($labelObject, $translate, $locale);
        }
    }
}
