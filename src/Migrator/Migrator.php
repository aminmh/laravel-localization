<?php

namespace Bugloos\LaravelLocalization\Migrator;

use Bugloos\LaravelLocalization\Abstract\AbstractMigrator;
use Bugloos\LaravelLocalization\Exceptions\MigratorFilesNotFoundException;
use Bugloos\LaravelLocalization\Migrator\LoaderStrategies\ArrayLoaderStrategy;
use Bugloos\LaravelLocalization\Migrator\LoaderStrategies\JsonLoaderStrategy;
use Bugloos\LaravelLocalization\Migrator\MigratorStrategies\ArrayMigrator;
use Bugloos\LaravelLocalization\Migrator\MigratorStrategies\JsonMigrator;
use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Models\Label;
use Bugloos\LaravelLocalization\Models\Translation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Migrator
{
    /**
     * @var array<AbstractMigrator> $strategies
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
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table(config('localization.tables')[Category::class])->truncate();
            DB::table(config('localization.tables')[Label::class])->truncate();
            DB::table(config('localization.tables')[Translation::class])->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

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
        $files = $this->getRecursiveDirAndFiles($path, $filter);

        if (empty($files)) {
            throw new MigratorFilesNotFoundException($path, 404);
        }

        $customStrategies = array_filter($files, static function ($filePath) {
            return !in_array(pathinfo($filePath, PATHINFO_EXTENSION), ['php', 'json']);
        });

        $this->initializeCustomStrategies($customStrategies);

        foreach ($files as $filePath) {
            $this->strategies[] = match (pathinfo($filePath, PATHINFO_EXTENSION)) {
                'php' => new ArrayMigrator(ArrayLoaderStrategy::loadByPath($filePath)),
                'json' => new JsonMigrator(JsonLoaderStrategy::loadByPath($filePath)),
                'yaml', 'yml' => null
            };
        }
    }

    private function initializeCustomStrategies(array $files): void
    {
        $customMigrators = config('localization.migrate.migrators');

        foreach ($files as $file) {
            if (array_key_exists($mimeType = pathinfo($file, PATHINFO_EXTENSION), $customMigrators)) {
                $loader = $customMigrators[$mimeType]['loader'];
                $this->strategies[] = new $customMigrators[$mimeType]['migrator'](new $loader($file));
            }
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
            throw new \BadMethodCallException(sprintf("The given path should be point to directory and also sub-directory of %s !", base_path('lang')), 400);
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
        if ($path === base_path('lang')) {
            return true;
        }

        if ($path === base_path()) {
            return false;
        }

        if (($parentDir = dirname($path)) !== base_path('lang')) {
            return $this->isSubDirectoryOfLang($parentDir);
        }

        return true;
    }
}
