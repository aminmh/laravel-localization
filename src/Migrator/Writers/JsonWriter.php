<?php

namespace Bugloos\LaravelLocalization\Migrator\Writers;

use Bugloos\LaravelLocalization\Abstract\AbstractMigratorResponse;
use Bugloos\LaravelLocalization\Abstract\AbstractWriter;
use Bugloos\LaravelLocalization\Contracts\PersistsWriteInterface;
use Bugloos\LaravelLocalization\Migrator\ReaderStrategies\ArrayReaderStrategy;
use Bugloos\LaravelLocalization\Views\Console\Console;
use Symfony\Component\Console\Output\ConsoleOutput;

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

                /** @var AbstractMigratorResponse $item */
                foreach ((new ArrayWriter($arrayReader))->save() as $item) {
                    if ($item && $item->isStatusOk()) {
                        (new Console())->success($item);
                    }
                }
            }

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }
}
