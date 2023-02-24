<?php

namespace Bugloos\LaravelLocalization\Abstract;

use Bugloos\LaravelLocalization\Translator;

abstract class AbstractWriter
{
    protected static Translator $translator;

    public function __construct(
        protected AbstractReader $reader
    ) {
    }

    /**
     * @param Translator $translator
     */
    public static function setTranslator(Translator $translator): void
    {
        self::$translator = $translator;
    }

    abstract public function save(): bool;
}
