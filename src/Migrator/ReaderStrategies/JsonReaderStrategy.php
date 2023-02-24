<?php

namespace Bugloos\LaravelLocalization\Migrator\ReaderStrategies;

use Bugloos\LaravelLocalization\Abstract\AbstractReader;

class JsonReaderStrategy extends AbstractReader
{
    public function readContent(string $path): array
    {
        try {
            $decodedJson = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $ex) {
            return [];
        }

        $recursiveIterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($decodedJson));

        $result = [];

        foreach ($recursiveIterator as $leaf) {
            $keys = [];

            foreach (range(0, $recursiveIterator->getDepth()) as $depth) {
                $keys[] = $recursiveIterator->getSubIterator($depth)?->key();
            }

            $result[implode('.', $keys)] = $leaf;
        }

        return $result;
    }

    public function guessLocale(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }
}
