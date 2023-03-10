<?php

namespace Bugloos\LaravelLocalization\Abstract;

use Bugloos\LaravelLocalization\Contracts\ExporterDataTransformerInterface;
use Bugloos\LaravelLocalization\Contracts\LazyCallExtractorInterface;
use Bugloos\LaravelLocalization\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Traversable;

abstract class AbstractExtractor implements LazyCallExtractorInterface, \IteratorAggregate
{
    private mixed $data;

    public function __construct(
        public readonly string $locale,
        public readonly string $category = '*'
    ) {
        if ($category === '*') {
            $this->data = $this->sourceQuery()->get();
        } else {
            $this->data = $this->sourceQuery()->firstWhere('name', $category);
        }
    }

    abstract public function write(string $path): bool;

    abstract protected function writableData(): string;

    abstract protected function fileName(): string;

    /**
     * @throws \Exception
     */
    public function lazyWrite(string $path): void
    {
        $dataBuffer = collect();

        foreach ($this as $item) {
            $dataBuffer->push($item);
        }

        $this->data = $dataBuffer;
        $this->write($path);
    }

    public function getIterator(): Traversable
    {
        foreach ($this->sourceQuery()->cursor() as $item) {
            yield $item;
        }
    }

    protected function getData(): array|\ArrayAccess
    {
        if ($this instanceof ExporterDataTransformerInterface) {
            return $this->transform($this->data);
        }

        return $this->data;
    }

    protected function sourceQuery(): Builder
    {
        return Category::with(['labels.translation' => function (Relation $query) {
            $query->whereRelation('locale', 'locale', $this->locale);
        }]);
    }
}
