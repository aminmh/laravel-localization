<?php

namespace Bugloos\LaravelLocalization\Migrator\MigratorStrategies;

use Bugloos\LaravelLocalization\Abstract\AbstractMigrator as BaseMigrator;
use Bugloos\LaravelLocalization\Migrator\LoaderStrategies\ArrayLoaderStrategy;

class JsonMigrator extends BaseMigrator
{
    public function migrate(): void
    {
        if (empty($data = $this->loader->getContent())) {
            return;
        }

        $locale = $this->loader->getLocale();

        foreach ($data as $category => $labelAndTranslate) {
            $arrayLoader = new ArrayLoaderStrategy();

            $arrayLoader->setCategory($category)
                ->setLocale($locale)
                ->setContent($labelAndTranslate);

            (new ArrayMigrator($arrayLoader))->migrate();
        }
    }
}
