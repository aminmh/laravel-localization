<?php

namespace Bugloos\LaravelLocalization\Abstract;

use Bugloos\LaravelLocalization\Contracts\CategorizedByPathInterface;

abstract class AbstractLoader
{

    protected array $translations;

    protected ?string $category = null;

    protected string $locale;

    public function __construct(protected ?string $path = null)
    {
    }

    public static function loadByPath(string $path): static
    {
        $newInstance = new static($path);
        $newInstance->setTranslations($newInstance->readFileContent());

        if ($newInstance instanceof CategorizedByPathInterface) {
            $newInstance->setCategory($newInstance->getCategoryFromPath($path));
        }

        $newInstance->setLocale($newInstance->extractLocaleFromFilePath($path));

        return $newInstance;

    }

    abstract public function readFileContent(): array;

    abstract public function extractLocaleFromFilePath(string $path): string;

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getTranslations(): array
    {
        return $this->translations;
    }

    public function setTranslations(array $translations): void
    {
        $this->translations = $translations;
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
