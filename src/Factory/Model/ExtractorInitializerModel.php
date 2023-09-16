<?php

namespace Bugloos\LaravelLocalization\Factory\Model;

class ExtractorInitializerModel
{
    public function __construct(
        public readonly string $locale,
        public readonly string $format,
    ) {
        $this->checkLocaleIsValid();
        $this->checkIsExtractorConfigured();
    }


    private function checkIsExtractorConfigured(): void
    {
        if (!array_key_exists($this->format, config('localization.extract.extractors'))) {
            throw new \UnexpectedValueException(sprintf('The %s not be configured in localization\'s extractors config!', $this->format));
        }
    }

    private function checkLocaleIsValid(): void
    {

        if (in_array($this->locale, \ResourceBundle::getLocales(''))) {
            return;
        }

        throw new \UnexpectedValueException(sprintf('Locale %s is invalid!', $this->locale));
    }
}
