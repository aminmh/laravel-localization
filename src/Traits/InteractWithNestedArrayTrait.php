<?php

namespace Bugloos\LaravelLocalization\Traits;

trait InteractWithNestedArrayTrait
{
    public function getOnlyNestedArray(array $data): array
    {
        return array_filter($data, static function (array|string $value, $key) {
            if (is_array($value)) {
                return $key;
            }
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function convertNestedArrayToFlatArray(array $nestedData): array
    {
        $recursiveIterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($nestedData));

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

    public function removeNestedArrayKeys(array &$data, array $keys): void
    {
        foreach ($keys as $key) {
            unset($data[$key]);
        }
    }

    protected function convertFlatArrayToNestedArray(array &$data): void
    {
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                $this->convertFlatArrayToNestedArray($value);
            }

            if (str_contains($key, '.')) {
                $sections = explode('.', $key);
                $category = $sections[0];
                $child = [implode('.', array_slice($sections, 1)) => $value];
                if (isset($data[$category])) {
                    $child = array_merge($data[$category], [implode('.', array_slice($sections, 1)) => $value]);
                }
                $data[$category] = $child;
                unset($data[$key]);
                $this->convertFlatArrayToNestedArray($data);
            }
        }
    }

    private function mergeAllItemsTogether(array $data): array
    {
        $result = [];

        foreach ($data as $item) {
            $result = [...$result, ...$item];
        }

        return $result;
    }
}
