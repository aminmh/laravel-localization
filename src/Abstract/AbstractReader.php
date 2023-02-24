<?php

namespace Bugloos\LaravelLocalization\Abstract;

use Bugloos\LaravelLocalization\Contracts\FileNameAsCategoryInterface;

abstract class AbstractReader
{
    protected string $path;

    protected array $content;

    protected ?string $category = null;

    protected string $locale;

    public function __construct(?string $path = null)
    {
        if ($path) {
            $this->path = $path;
            $this->setContent($this->readContent($path));
            if ($this instanceof FileNameAsCategoryInterface) {
                $this->setCategory($this->guessCategoryName($path));
            }
            $this->setLocale($this->guessLocale($path));
        }
    }

    abstract public function readContent(string $path): array;

    abstract public function guessLocale(string $path): string;

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function setContent(array $content): void
    {
        $this->content = $content;
    }

    public function setCategory(?string $category = null): self
    {
        $this->category = $category;
        return $this;
    }

    public function setLocale(?string $locale = null): self
    {
        $this->locale = $locale;
        return $this;
    }
}
