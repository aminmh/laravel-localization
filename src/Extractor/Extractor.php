<?php

namespace Bugloos\LaravelLocalization\Extractor;

use Bugloos\LaravelLocalization\Abstract\AbstractExtractor as ExtractorType;

class Extractor
{
    public static function extract(ExtractorType $extractor, ?string $path = null): void
    {
        if (!$path) {
            $path = static::path();
        }

        $extractor->write($path);
    }

    private static function path(): string
    {
        return config('localization.extract.export_path');
    }
}
