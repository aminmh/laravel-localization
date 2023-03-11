<?php

namespace Bugloos\LaravelLocalization\Migrator;

use Bugloos\LaravelLocalization\Abstract\AbstractWriter;
use Bugloos\LaravelLocalization\Migrator\LoaderStrategies\JsonLoaderStrategy;
use Bugloos\LaravelLocalization\Migrator\LoaderStrategies\ArrayLoaderStrategy;
use Bugloos\LaravelLocalization\Migrator\MigratorStrategies\JsonMigrator;
use Bugloos\LaravelLocalization\Migrator\MigratorStrategies\ArrayMigrator;
use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Models\Label;
use Bugloos\LaravelLocalization\Models\Translation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Migrator
{
    /**
     * @var array<AbstractWriter> $strategies
     */
    private array $strategies = [];

    public function load(string $path, array $filter = []): void
    {
        $this->initializeStrategies($path, $filter);
        $this->migrate();
    }

    public function purge(): bool
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

    private function migrate(): void
    {
        foreach ($this->strategies as $strategy) {
            $strategy->migrate();
        }
    }

    private function initializeStrategies(string $path, array $filter = []): void
    {
        foreach ($this->getRecursiveDirAndFiles($path, $filter) as $filePath) {
            $this->strategies[] = match (pathinfo($filePath, PATHINFO_EXTENSION)) {
                'php' => new ArrayMigrator(new ArrayLoaderStrategy($filePath)),
                'json' => new JsonMigrator(new JsonLoaderStrategy($filePath)),
                'yaml', 'yml' => null
            };
        }
    }

    private function getRecursiveDirAndFiles(string $path, array $filter = []): array
    {
        $dirOrFiles = $this->normalizePath(realpath($path), $filter);

        $result = [];

        foreach ($dirOrFiles as $dirOrFile) {
            if (is_file($dirOrFile)) {
                $result[] = $dirOrFile;
                continue;
            }

            $absolutePath = $path . DIRECTORY_SEPARATOR . $dirOrFile;

            $result[] = $this->getRecursiveDirAndFiles($absolutePath);
        }

        return Arr::flatten($result);
    }

    private function normalizePath(string $path, array $filter = []): array
    {
        if (!$this->isSubDirectoryOfLang($path)) {
            throw new \BadMethodCallException(sprintf("The given path should be point to directory and also sub-directory of %s !", base_path('/lang')), 400);
        }

        if (is_dir($path)) {
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
        if ($path === base_path()) {
            return false;
        }

        if (($parentDir = dirname($path)) !== base_path('lang')) {
            return $this->isSubDirectoryOfLang($parentDir);
        }

        return true;
    }
}
