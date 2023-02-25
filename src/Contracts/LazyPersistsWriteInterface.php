<?php

namespace Bugloos\LaravelLocalization\Contracts;

interface LazyPersistsWriteInterface
{
    public function save(): \Generator;
}
