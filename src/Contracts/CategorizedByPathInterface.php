<?php

namespace Bugloos\LaravelLocalization\Contracts;

interface CategorizedByPathInterface
{
    public function getCategoryFromPath(string $path): string;
}
