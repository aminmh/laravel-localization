<?php

namespace Bugloos\LaravelLocalization\Extractor\ExtractorTypes;

use Bugloos\LaravelLocalization\Abstract\AbstractExtractor;
use Bugloos\LaravelLocalization\Contracts\ExporterDataTransformerInterface;
use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Traits\InteractWithNestedArrayTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Enumerable;
use IteratorAggregate;
use Traversable;

class ArrayExtractor extends AbstractExtractor implements IteratorAggregate, ExporterDataTransformerInterface
{
    use InteractWithNestedArrayTrait;

    public function write(string $path): bool
    {
        $path = sprintf('%s/%s', rtrim($path, '/'), $this->fileName());
        $content = str_replace("{{ARRAY_CONTENT}}", $this->writableData(), $this->stub());
        return file_put_contents($path, $content) || false;
    }

    public function transform(mixed $data): array
    {
        if ($data instanceof Enumerable) {
            $labels = $data->map(static function (Category $item) {
                return $item->labels()->get()->pluck('translation.text', 'key')->toArray();
            })->toArray();
        } else {
            $labels = Arr::pluck($data->labels, 'translation.text', 'key');
        }

        $this->convertFlat2NestedArray($labels);

        return $labels;
    }

    public function getIterator(): Traversable
    {
        if ($this->category === '*') {
            foreach ($this->sourceQuery()->cursor() as $item) {
                yield $item;
            }
        }
    }

    protected function fileName(): string
    {
        return sprintf("%s_%s.php", $this->category, strtoupper($this->locale));
    }

    protected function writableData(): string
    {
        $data = $this->getData();

        if (array_is_list($data)) {
            $result = [];

            foreach ($data as $item) {
                $result = [...$result, ...$item];
            }

            $data = $result;
        }

        return str_replace(['array (', ')'], ['[', ']'], var_export($data, true));
    }

    private function stub(): string
    {
        return <<<STUB
<?php

return
    {{ARRAY_CONTENT}};
STUB;
    }
}
