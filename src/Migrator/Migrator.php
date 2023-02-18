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
                'json',
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
        if (is_dir($path)) {
            return array_diff(scandir($path), ['.', '..']);
        }

        return [$path];
    }
}
