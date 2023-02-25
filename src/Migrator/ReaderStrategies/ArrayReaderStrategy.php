<?php

namespace Bugloos\LaravelLocalization\Migrator\ReaderStrategies;

use Bugloos\LaravelLocalization\Abstract\AbstractReader;
use Bugloos\LaravelLocalization\Contracts\FileNameAsCategoryInterface;

class ArrayReaderStrategy extends AbstractReader implements FileNameAsCategoryInterface
{
    public function readContent(string $path): array
    {
        return require $path;
    }

    public function guessCategoryName(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    public function guessLocale(string $path): string
    {
        $directory = dirname($path);

        return substr($directory, strrpos($directory, '/') + 1);
    }
}
