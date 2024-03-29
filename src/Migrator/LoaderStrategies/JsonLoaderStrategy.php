<?php

namespace Bugloos\LaravelLocalization\Migrator\LoaderStrategies;

use Bugloos\LaravelLocalization\Abstract\AbstractLoader;
use Bugloos\LaravelLocalization\Traits\InteractWithNestedArrayTrait;

class JsonLoaderStrategy extends AbstractLoader
{
    use InteractWithNestedArrayTrait;

    public function readFileContent(): array
    {
        try {
            $decodedJson = json_decode(file_get_contents($this->path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return [];
        }

        return $this->extract($this->convertNestedArrayToFlatArray($decodedJson));
    }

    public function extractLocaleFromFilePath(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    private function extract(array $flattenArray): array
    {
        $data = [];

        while ($payload = key($flattenArray)) {
            $sections = explode('.', $payload);
            $category = $sections[0];

            if (!array_key_exists($category, $data)) {
                $data[$category] = [];
            }

            $label = implode('.', array_slice($sections, 1));
            $data[$category][$label] = reset($flattenArray);
            unset($flattenArray[$payload]);
        }

        return $data;
    }
}
