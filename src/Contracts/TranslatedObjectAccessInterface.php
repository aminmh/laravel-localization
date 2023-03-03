<?php

namespace Bugloos\LaravelLocalization\Contracts;

use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Models\Label;
use Bugloos\LaravelLocalization\Models\Language;

interface TranslatedObjectAccessInterface
{
    public function getLabelObject(): Label;

    public function getLocaleObject(): Language;

    public function getCategoryObject(): Category;
}
