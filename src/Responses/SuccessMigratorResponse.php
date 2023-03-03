<?php

namespace Bugloos\LaravelLocalization\Responses;

use Bugloos\LaravelLocalization\Abstract\AbstractMigratorResponse;
use Bugloos\LaravelLocalization\Contracts\TranslatedObjectAccessInterface;
use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Models\Label;
use Bugloos\LaravelLocalization\Models\Language;
use Bugloos\LaravelLocalization\Models\Translation;

class SuccessMigratorResponse extends AbstractMigratorResponse implements TranslatedObjectAccessInterface
{
    public function __construct(
        public readonly Translation $translate
    ) {
        $this->setStatusOk(true);
    }

    public function __toString(): string
    {
        return sprintf(
            "Label <options=bold,underscore>%s</> from category <options=bold,underscore>%s</> translate to <options=bold,underscore>%s</> with <options=bold,underscore>%s</> language.",
            $this->getLabelObject()->key,
            $this->getCategoryObject()->name,
            $this->translate->text,
            $this->getLocaleObject()->locale
        );
    }

    public function getLabelObject(): Label
    {
        return $this->translate->label;
    }

    public function getLocaleObject(): Language
    {
        return $this->translate->locale;
    }

    public function getCategoryObject(): Category
    {
        return $this->getLabelObject()->category;
    }
}
