<?php

namespace Bugloos\LaravelLocalization\Contracts;

interface FileNameAsCategoryInterface
{
    public function guessCategoryName(string $path): string;
}
