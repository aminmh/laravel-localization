<?php

namespace Bugloos\LaravelLocalization;

use Bugloos\LaravelLocalization\Models\Language;
use Illuminate\Translation\FileLoader;

class Loader extends FileLoader
{
    public function load($locale, $group, $namespace = null)
    {
        $lines = parent::load($locale, $group, $namespace);

        if (!count($lines)) {
            return $this->loadFromDB($locale, $group);
        }

        return $lines;
    }

    protected function loadFromDB($locale, $group)
    {
        $locales = Language::query()
            ->where('locale', $locale)
            ->orWhere('country', $locale)
            ->get()
            ->pluck('locale');
    }
}
