<?php

namespace Bugloos\LaravelLocalization\Migrator;

use Bugloos\LaravelLocalization\Migrator\ReaderStrategies\JsonReaderStrategy;
use Bugloos\LaravelLocalization\Migrator\ReaderStrategies\PhpReaderStrategy;
use Bugloos\LaravelLocalization\Migrator\Writers\JsonWriter;
use Bugloos\LaravelLocalization\Migrator\Writers\PhpWriter;
use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Models\Label;
use Bugloos\LaravelLocalization\Models\Translation;
use Bugloos\LaravelLocalization\Translator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Migrator
{
    /**
     * @var array<PhpWriter> $strategies
     */
    private array $strategies = [];

    public function __construct(private readonly Translator $translator)
    {
    }

    public function load(string $path, array $filter = []): void
    {
        foreach ($this->getRecursiveDirAndFiles($path, $filter) as $filePath) {
            $this->strategies[] = match (pathinfo($filePath, PATHINFO_EXTENSION)) {
                'php' => new PhpWriter(new PhpReaderStrategy($filePath)),
                'json' => new JsonWriter(new JsonReaderStrategy($filePath)),
                'yaml', 'yml' => null
            };
        }

        $this->migrate();
    }

    private function migrate(): void
    {
        foreach ($this->strategies as $strategy) {
            $strategy->save();
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
        if (is_dir($path)) {
            $dirs = array_diff(scandir($path), ['.', '..']);

            if (!empty($filter)) {
                return array_filter($dirs, static fn (string $dir) => in_array($dir, $filter, true));
            }
            return $dirs;
        }

        return [$path];
    }
}
