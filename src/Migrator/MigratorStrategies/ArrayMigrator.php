<?php

namespace Bugloos\LaravelLocalization\Migrator\MigratorStrategies;

use Bugloos\LaravelLocalization\Abstract\AbstractMigrator as BaseMigrator;
use Bugloos\LaravelLocalization\Exceptions\TranslationFailureException;

class ArrayMigrator extends BaseMigrator
{
    public function migrate(): void
    {
        $categoryObject = static::$translator->addCategory($this->loader->getCategory());

        $locale = $this->loader->getLocale();

        $data = $this->loader->getTranslations();

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
