<?php

namespace Bugloos\LaravelLocalization\Migrator;

use Bugloos\LaravelLocalization\Translator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use JsonException;

class Migrator
{
    public function __construct(private readonly Translator $translator)
    {
    }

    /**
     * @throws JsonException
     */
    public function migrate(string $path): void
    {
        foreach ($this->getRecursiveDirAndFiles($path) as $filePath) {
            match (pathinfo($filePath, PATHINFO_EXTENSION)) {
                'php' => $this->loadAndStoreArray(require $filePath, $this->parsePhpPath($filePath)['category'], $this->parsePhpPath($filePath)['locale']),
                'json' => $this->normalizeFlatArray2Associate($this->convertNestedJson2FlatArray($filePath), $this->parseJsonPath($filePath)['locale']),
                'yaml', 'yml' => null
            };
        }
    }

    private function parsePhpPath(string $path): array
    {
        $directory = dirname($path);

        return [
            'category' => pathinfo($path, PATHINFO_FILENAME),
            'locale' => substr($directory, strrpos($directory, '/') + 1) // var/www/html/routes/../lang/en => en
        ];
    }

    private function parseJsonPath(string $path): array
    {
        return [
            'locale' => pathinfo($path, PATHINFO_FILENAME)
        ];
    }

    /**
     * @throws JsonException
     */
    public function convertNestedJson2FlatArray(string $path): array
    {
        $decodedJson = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

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

    public function normalizeFlatArray2Associate(array $normalizedData, string $locale): bool
    {
        $data = [];

        try {
            while ($payload = key($normalizedData)) {

                $sections = explode('.', $payload);
                $category = $sections[0];

                if (!array_key_exists($category, $data)) {
                    $data[$category] = [];
                }

                $label = implode('.', array_slice($sections, 1));
                $data[$category][$label] = reset($normalizedData);
                unset($normalizedData[$payload]);
            }

            foreach ($data as $category => $labelAndTranslate) {
                $this->loadAndStoreArray($labelAndTranslate, $category, $locale);
            }

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    private function loadAndStoreArray(array $labelsAndTranslate, string $category, string $locale): bool
    {
        try {
            $categoryObject = $this->translator->addCategory($category);

            foreach ($labelsAndTranslate as $label => $translate) {
                $labelObject = $this->translator->addLabel($label, $categoryObject);

                $this->translator->translate($labelObject, $translate, $locale);
            }

            return true;

        } catch (QueryException $ex) {
            return false;
        }

    }

    public function getRecursiveDirAndFiles(string $path): array
    {
        $dirOrFiles = $this->normalizePath($path);

        $result = [];

        foreach ($dirOrFiles as $dirOrFile) {

            $currentPath = $path . DIRECTORY_SEPARATOR . $dirOrFile;

            if (is_dir($currentPath)) {
                $result[] = $this->getRecursiveDirAndFiles($currentPath);
            } else {
                $result[] = $currentPath; //Absolutely is File
            }
        }

        return Arr::flatten($result);
    }

    private function normalizePath(string $path): array
    {
        if (is_dir(realpath($path))) {
            return array_diff(scandir($path), ['.', '..']);
        }

        return [$path];
    }
}
