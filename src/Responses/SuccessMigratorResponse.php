<?php

namespace Bugloos\LaravelLocalization\Responses;

use Bugloos\LaravelLocalization\Views\Console\ConsoleOutput;

class SuccessMigratorResponse implements \Stringable
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
        return ConsoleOutput::write(sprintf(
            "Label %s from category %s translate to %s with %s language.",
            $this->label,
            $this->category,
            $this->translate,
            $this->locale
        ), ConsoleOutput::CONSOLE_TEXT_GREEN);
    }
}
