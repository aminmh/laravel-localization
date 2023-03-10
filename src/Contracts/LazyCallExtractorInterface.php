<?php

namespace Bugloos\LaravelLocalization\Contracts;

interface LazyCallExtractorInterface
{
    public function lazyWrite(string $path): void;
}
