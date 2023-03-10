<?php

namespace Bugloos\LaravelLocalization\Extractor;

use Bugloos\LaravelLocalization\Abstract\AbstractExtractor as ExtractorType;
use Bugloos\LaravelLocalization\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class Extractor
{
    public static function extract(ExtractorType $extractor): void
    {
        $extractor->write(storage_path('/app/public/'));
    }

    public static function lazyExtract(ExtractorType $extractor): void
    {
        $extractor->lazyWrite(storage_path('/app/public/'));
    }
}
