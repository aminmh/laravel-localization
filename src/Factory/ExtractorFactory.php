<?php

namespace Bugloos\LaravelLocalization\Factory;

use Bugloos\LaravelLocalization\Factory\Model\ExtractorInitializerModel;
use Bugloos\LaravelLocalization\Abstract\AbstractExtractor;

class ExtractorFactory
{
    public static function createByModel(ExtractorInitializerModel $extractorModel): AbstractExtractor
    {
        $extractor = config('localization.extract.extractors')[$extractorModel->format];

        return new $extractor($extractorModel->locale);
    }
}
