<?php

namespace Bugloos\LaravelLocalization\Extractor\ExtractorTypes;

use Bugloos\LaravelLocalization\Abstract\AbstractExtractor;
use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Traits\InteractWithNestedArrayTrait;

class JsonExtractor extends AbstractExtractor
{
    use InteractWithNestedArrayTrait;

    public function write(string $path): bool
    {
        return file_put_contents(sprintf("%s/%s", rtrim($path, '/'), $this->fileName()), $this->makeWritableToFile());
    }

    protected function transform(array $data): array
    {
        $data = collect($data);

        $categories = $data->map(static fn (Category $category) => [
            $category->name => $category
                ->labels()
                ->get()
                ->pluck('translation.text', 'key')
                ->toArray()
        ])->toArray();

        foreach ($categories as &$labels) {
            $this->convertFlatArrayToNestedArray($labels);
        }

        return $categories;
    }

    /**
     * @throws \JsonException
     */
    protected function makeWritableToFile(): string
    {
        $data = $this->data;

        if (array_is_list($this->data)) {
            $data = $this->mergeAllItemsTogether($data);
        }

        return json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }

    protected function fileName(): string
    {
        return $this->locale . '.json';
    }
}
