<?php

namespace Bugloos\LaravelLocalization\Migrator\LoaderStrategies;

use Bugloos\LaravelLocalization\Abstract\AbstractLoader;
use Bugloos\LaravelLocalization\Contracts\FileNameAsCategoryInterface;
use Bugloos\LaravelLocalization\Traits\InteractWithNestedArrayTrait;

class ArrayLoaderStrategy extends AbstractLoader implements FileNameAsCategoryInterface
{
    use InteractWithNestedArrayTrait;

    public function readContent(string $path): array
    {
        $data = require $path;
        $nestedData = $this->getOnlyNestedArray($data);

        if (!empty($nestedData)) {
            $flattenData = $this->convertNestedArrayToFlatArray($nestedData);
            $this->removeNestedArrayKeys($data, array_keys($nestedData));
            $data = array_merge($data, $flattenData);
        }

        return $data;
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
