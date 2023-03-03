<?php

namespace Bugloos\LaravelLocalization\Migrator\Writers;

use Bugloos\LaravelLocalization\Abstract\AbstractWriter;
use Bugloos\LaravelLocalization\Contracts\PersistsWriteInterface;
use Bugloos\LaravelLocalization\Migrator\ReaderStrategies\ArrayReaderStrategy;
use Bugloos\LaravelLocalization\Responses\MigratorResponse;
use Bugloos\LaravelLocalization\Traits\LazyResponseTrait;
use Bugloos\LaravelLocalization\Views\Console\Console;

class JsonWriter extends AbstractWriter implements PersistsWriteInterface
{
    use LazyResponseTrait;

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

//                $this->iterateResponse(
//                    new ArrayWriter($arrayReader),
//                    successCallback: static function (MigratorResponse $result) {
//                        (new Console())->success($result);
//                    },
//                    failedCallback: static function (MigratorResponse $result) {
//                        (new Console())->error($result);
//                    }
//                );

                $this->skipOnFailResponse(new ArrayWriter($arrayReader), static function (MigratorResponse $result) {
                    (new Console())->success($result);
                });
            }

            return true;
        } catch (\Exception) {
            return false;
        }
    }
}
