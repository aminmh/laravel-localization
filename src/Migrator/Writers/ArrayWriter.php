<?php

namespace Bugloos\LaravelLocalization\Migrator\Writers;

use Bugloos\LaravelLocalization\Abstract\AbstractWriter;
use Bugloos\LaravelLocalization\Contracts\LazyPersistsWriteInterface;
use Bugloos\LaravelLocalization\Exceptions\LocalizationResourceException;
use Bugloos\LaravelLocalization\Responses\FailedMigratorResponse;
use Bugloos\LaravelLocalization\Responses\SuccessMigratorResponse;
use Illuminate\Database\QueryException;

class ArrayWriter extends AbstractWriter implements LazyPersistsWriteInterface
{
    public function save(): \Generator
    {
        try {
            $categoryObject = static::$translator->addCategory($category = $this->reader->getCategory());

            $locale = $this->reader->getLocale();

            foreach ($this->reader->getContent() as $label => $translate) {
                $labelObject = static::$translator->addLabel($label, $categoryObject);

                $translated = static::$translator->translate($labelObject, $translate, $locale);

                yield (new SuccessMigratorResponse($translated));
            }
        } catch (LocalizationResourceException $ex) {
            yield (new FailedMigratorResponse($label, $category, $translate, $locale));
        }
//        catch (QueryException $ex) {
//            yield (new FailedMigratorResponse($label, $category, $translate, $locale));
//        }
    }
}
