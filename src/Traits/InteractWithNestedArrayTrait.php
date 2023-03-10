<?php

namespace Bugloos\LaravelLocalization\Traits;

trait InteractWithNestedArrayTrait
{
    public function getOnlyNested(array $data): array
    {
        return array_filter($data, static function (array|string $value, $key) {
            if (is_array($value)) {
                return $key;
            }
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function convertNested2FlatArray(array $nestedData)
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

    public function removeNestedKeys(array &$data, array $keys): void
    {
        foreach ($keys as $key) {
            unset($data[$key]);
        }
    }

    protected function convertFlat2NestedArray(array &$data): void
    {
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                $this->convertFlat2NestedArray($value);
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
                $this->convertFlat2NestedArray($data);
            }
        }
    }
}
