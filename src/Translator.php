<?php

namespace Bugloos\LaravelLocalization;

use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Support\NamespacedItemResolver;

class Translator implements TranslatorContract
{
    public function __construct(protected NamespacedItemResolver $namespaceResolver)
    {
    }

    public function get($key, array $replace = [], $locale = null)
    {
    }

    public function choice($key, $number, array $replace = [], $locale = null)
    {
    }

    public function getLocale()
    {
    }

    public function setLocale($locale)
    {
    }
}
