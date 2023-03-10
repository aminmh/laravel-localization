<?php

namespace Bugloos\LaravelLocalization\Contracts;

interface ExporterDataTransformerInterface
{
    public function transform(mixed $data): mixed;
}
