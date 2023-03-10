<?php

namespace Bugloos\LaravelLocalization\Extractor\ExtractorTypes;

use Bugloos\LaravelLocalization\Abstract\AbstractExtractor;
use Bugloos\LaravelLocalization\Contracts\ExporterDataTransformerInterface;
use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Traits\InteractWithNestedArrayTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Enumerable;
use Traversable;

class JsonExtractor extends AbstractExtractor implements ExporterDataTransformerInterface
{
    use InteractWithNestedArrayTrait;

    public function write(string $path): bool
    {
        return file_put_contents(sprintf("%s/%s", rtrim($path, '/'), $this->fileName()), $this->writableData()) || false;
    }

    public function transform(mixed $data): array
    {
        if (!$data instanceof Enumerable) {
            $data = collect([$data]);
        }

        $categories = $data->map(static fn (Category $category) => [
            $category->name => $category
                ->labels()
                ->get()
                ->pluck('translation.text', 'key')
                ->toArray()
        ])->toArray();

        foreach ($categories as &$labels) {
            $this->convertFlat2NestedArray($labels);
        }

        return $categories;
    }

    /**
     * @throws \JsonException
     */
    protected function writableData(): string
    {
        $data = $this->getData();

        if (array_is_list($data)) {
            $data = $this->mergeAllItemsTogether($data);
        }

        return json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }

    protected function fileName(): string
    {
        return $this->locale . '.json';
    }
}
