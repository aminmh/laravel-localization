<?php

namespace Bugloos\LaravelLocalization\Extractor\ExtractorTypes;

use Bugloos\LaravelLocalization\Abstract\AbstractExtractor;
use Bugloos\LaravelLocalization\Contracts\ExporterDataTransformerInterface;
use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Traits\InteractWithNestedArrayTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Enumerable;

class ArrayExtractor extends AbstractExtractor implements ExporterDataTransformerInterface
{
    use InteractWithNestedArrayTrait;

    public function write(string $path): bool
    {
        $path = sprintf('%s/%s', rtrim($path, '/'), $this->fileName());
        $content = str_replace("{{ARRAY_CONTENT}}", $this->makeWritableToFile(), $this->stub());
        return file_put_contents($path, $content);
    }

    public function transform(array $data): array
    {
        $data = collect($data);

        $labels = $data->map(
            static fn (Category $item) => $item->labels()->get()->pluck('translation.text', 'key')->toArray()
        )->toArray();


        $this->convertFlatArrayToNestedArray($labels);

        return $labels;
    }

    protected function fileName(): string
    {
        return sprintf("%s_%s.php", $this->category, strtoupper($this->locale));
    }

    protected function makeWritableToFile(): string
    {
        $data = $this->data;

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
