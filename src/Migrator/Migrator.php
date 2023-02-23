<?php

namespace Bugloos\LaravelLocalization\Migrator;

use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Models\Label;
use Bugloos\LaravelLocalization\Models\Translation;
use Bugloos\LaravelLocalization\Translator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use JsonException;

class Migrator
{
    public function __construct(private readonly Translator $translator)
    {
    }

    /**
     * @throws JsonException
     */
    public function migrate(string $path, array $filter = []): void
    {
        foreach ($this->getRecursiveDirAndFiles($path, $filter) as $filePath) {
            match (pathinfo($filePath, PATHINFO_EXTENSION)) {
                'php' => $this->loadAndStoreArray(require $filePath, $this->parsePhpPath($filePath)['category'], $this->parsePhpPath($filePath)['locale']),
                'json' => $this->normalizeFlatArray2Associate($this->convertNestedJson2FlatArray($filePath), $this->parseJsonPath($filePath)['locale']),
                'yaml', 'yml' => null
            };
        }
    }

    public function refresh(): bool
    {
        try {
            DB::table(config('localization.tables')[Translation::class])->truncate();
            DB::table(config('localization.tables')[Label::class])->truncate();
            DB::table(config('localization.tables')[Category::class])->truncate();

            return true;
        } catch (QueryException $ex) {
            return false;
        }
    }

    protected function parsePhpPath(string $path): array
    {
        $directory = dirname($path);

        return [
            'category' => pathinfo($path, PATHINFO_FILENAME),
            'locale' => substr($directory, strrpos($directory, '/') + 1) // var/www/html/routes/../lang/en => en
        ];
    }

    protected function parseJsonPath(string $path): array
    {
        return [
            'locale' => pathinfo($path, PATHINFO_FILENAME)
        ];
    }

    /**
     * @throws JsonException
     */
    private function convertNestedJson2FlatArray(string $path): array
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

    private function normalizeFlatArray2Associate(array $normalizedData, string $locale): bool
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

    private function getRecursiveDirAndFiles(string $path, array $filter = []): array
    {
        $dirOrFiles = $this->normalizePath($path, $filter);

        $result = [];

        foreach ($dirOrFiles as $dirOrFile) {
            $currentPath = $path . DIRECTORY_SEPARATOR . $dirOrFile;

            if (!is_dir($currentPath)) {
                $result[] = $currentPath;
                continue;
            }

            $result[] = $this->getRecursiveDirAndFiles($currentPath);
        }

        return Arr::flatten($result);
    }

    private function normalizePath(string $path, array $filter = []): array
    {
        if (!$this->isSubDirectoryOfLang($path)) {
            throw new \BadMethodCallException(sprintf("The given path should be point to directory and also sub-directory of %s !", base_path('/lang')), 400);
        }

        if (is_dir(realpath($path))) {
            $dirs = array_diff(scandir($path), ['.', '..']);

            if (!empty($filter)) {
                return array_filter($dirs, static fn (string $dir) => in_array($dir, $filter, true));
            }
            return $dirs;
        }

        return [$path];
    }

    private function isSubDirectoryOfLang(string $path): bool
    {
        if (!is_dir($path) || $path === base_path()) {
            return false;
        }

        if (($parentDir = dirname($path)) !== base_path('/lang')) {
            return $this->isSubDirectoryOfLang($parentDir);
        }

        return true;
    }
}
