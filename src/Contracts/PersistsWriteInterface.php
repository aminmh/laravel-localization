<?php

namespace Bugloos\LaravelLocalization\Contracts;

interface PersistsWriteInterface
{
    public function save(): bool;
}
