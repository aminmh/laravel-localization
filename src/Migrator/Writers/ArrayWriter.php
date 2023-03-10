<?php

namespace Bugloos\LaravelLocalization\Migrator\Writers;

use Bugloos\LaravelLocalization\Abstract\AbstractWriter;
use Bugloos\LaravelLocalization\Contracts\LazyPersistsWriteInterface;
use Bugloos\LaravelLocalization\DTO\TranslatedDTO;
use Bugloos\LaravelLocalization\Exceptions\LocalizationResourceException;
use Bugloos\LaravelLocalization\Exceptions\TranslationFailureException;
use Bugloos\LaravelLocalization\Responses\MigratorResponse;

class ArrayWriter extends AbstractWriter implements LazyPersistsWriteInterface
{
    public function save(): \Generator
    {
        try {
            $categoryObject = static::$translator->addCategory($this->reader->getCategory());

            $locale = $this->reader->getLocale();

            foreach ($this->reader->getContent() as $label => $translate) {
                $labelObject = static::$translator->addLabel($label, $categoryObject);

                $translated = static::$translator->translate($labelObject, $translate, $locale);

                $response = new MigratorResponse(true);

                $response->setTranslatedResource(new TranslatedDTO($translated));

                yield $response;
            }
        } catch (LocalizationResourceException $ex) {
            yield new MigratorResponse(false, $ex->getMessage());
        } catch (TranslationFailureException $failureException) {
            yield new MigratorResponse(false, $failureException->getMessage());
        }
    }
}
