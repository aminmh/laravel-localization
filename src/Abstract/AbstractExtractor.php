<?php

namespace Bugloos\LaravelLocalization\Abstract;

use Bugloos\LaravelLocalization\Models\Category;
use Illuminate\Database\Eloquent as Eloquent;

abstract class AbstractExtractor
{
    protected mixed $data;

    protected string $category = '*';

    public function __construct(
        public readonly string $locale
    ) {
        $this->data = $this->transform($this->getData());
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    abstract public function write(string $path): bool;

    abstract protected function makeWritableToFile(): string;

    abstract protected function fileName(): string;

    protected function sourceQuery(): Eloquent\Builder
    {
        return Category::with(['labels.translation' => function (Eloquent\Relations\Relation $query) {
            $query->whereRelation('locale', 'locale', $this->locale);
        }]);
    }

    protected function transform(array $data): array
    {
        return $data;
    }

    private function getData(): array
    {
        if ($this->category === '*') {
            return $this->sourceQuery()->get()->toArray();
        }

        return [$this->sourceQuery()->firstWhere('name', $this->category)];
    }
}
