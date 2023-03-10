<?php

namespace Bugloos\LaravelLocalization\Migrator\ReaderStrategies;

use Bugloos\LaravelLocalization\Abstract\AbstractReader;
use Bugloos\LaravelLocalization\Traits\InteractWithNestedArrayTrait;

class JsonReaderStrategy extends AbstractReader
{
    use InteractWithNestedArrayTrait;

    public function readContent(string $path): array
    {
        try {
            $decodedJson = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return [];
        }

        return $this->normalizeArray($this->convertNested2FlatArray($decodedJson));
    }

    public function guessLocale(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    private function normalizeArray(array $flattenArray): array
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
