<?php

namespace Bugloos\LaravelLocalization\DTO;

use Bugloos\LaravelLocalization\Enums\ResourceExceptionMessages as ExceptionMessages;

class FailedTranslationDTO implements \Stringable
{
    public function __construct(
        public readonly string $label,
        public readonly string $category,
        public readonly string $translate,
        public readonly string $locale
    ) {
    }

    public function __toString(): string
    {
        return sprintf(ExceptionMessages::FAILED_TRANSLATION->value, $this->label, $this->category, $this->locale);
    }
}
