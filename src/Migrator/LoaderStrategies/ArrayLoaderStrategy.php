<?php

namespace Bugloos\LaravelLocalization\Migrator\LoaderStrategies;

use Bugloos\LaravelLocalization\Abstract\AbstractLoader;
use Bugloos\LaravelLocalization\Contracts\CategorizedByPathInterface;
use Bugloos\LaravelLocalization\Traits\InteractWithNestedArrayTrait;

class ArrayLoaderStrategy extends AbstractLoader implements CategorizedByPathInterface
{
    use InteractWithNestedArrayTrait;

    public function readFileContent(): array
    {
        $data = require $this->path;
        $nestedArray = $this->getNestedArray($data);

        if (!empty($nestedArray)) {
            $data = $this->normalizeAndMerge($data, $nestedArray);
        }

        return $data;
    }

    public function getCategoryFromPath(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    public function extractLocaleFromFilePath(string $path): string
    {
        $directory = dirname($path);

        return substr($directory, strrpos($directory, '/') + 1);
    }

    private function normalizeAndMerge(array $source, array $nestedArray): array
    {
        $nestedArrayKeys = array_keys($nestedArray);
        $distinctSource = array_filter($source, static fn ($key) => !in_array($key, $nestedArrayKeys, true), ARRAY_FILTER_USE_KEY);
        $flattenArray = $this->convertNestedArrayToFlatArray($nestedArray);
        return array_merge($distinctSource, $flattenArray);
    }
}
