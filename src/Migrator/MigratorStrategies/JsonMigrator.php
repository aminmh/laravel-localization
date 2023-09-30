<?php

namespace Bugloos\LaravelLocalization\Migrator\MigratorStrategies;

use Bugloos\LaravelLocalization\Abstract\AbstractMigrator as BaseMigrator;
use Bugloos\LaravelLocalization\Migrator\LoaderStrategies\ArrayLoaderStrategy;

class JsonMigrator extends BaseMigrator
{
    public function migrate(): void
    {
        $translations = $this->loader->getTranslations();

        if (empty($translations)) {
            return;
        }

        $locale = $this->loader->getLocale();

        foreach ($translations as $category => $labelAndTranslate) {
            $arrayLoader = new ArrayLoaderStrategy();

            $arrayLoader->setCategory($category)
                ->setLocale($locale)
                ->setTranslations($labelAndTranslate);

            (new ArrayMigrator($arrayLoader))->migrate();
        }
    }
}
