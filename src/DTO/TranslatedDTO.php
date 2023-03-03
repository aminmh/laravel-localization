<?php

namespace Bugloos\LaravelLocalization\DTO;

use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Models\Label;
use Bugloos\LaravelLocalization\Models\Language;
use Bugloos\LaravelLocalization\Models\Translation;

class TranslatedDTO
{
    public function __construct(
        public readonly Translation $translate
    ) {
    }

    public function getLabel(): Label
    {
        return $this->translate->label;
    }

    public function getLocale(): Language
    {
        return $this->translate->locale;
    }

    public function getCategory(): Category
    {
        return $this->getLabel()->category;
    }
}
