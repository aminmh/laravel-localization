<?php

namespace Bugloos\LaravelLocalization\Abstract;

use Bugloos\LaravelLocalization\Translator;

abstract class AbstractMigrator
{
    protected static Translator $translator;

    public function __construct(
        protected AbstractLoader $loader
    ) {
    }

    abstract public function migrate(): void;

    /**
     * @param Translator $translator
     */
    public static function setTranslator(Translator $translator): void
    {
        self::$translator = $translator;
    }
}
