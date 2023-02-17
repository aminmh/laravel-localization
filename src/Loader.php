<?php

namespace Bugloos\LaravelLocalization;

use Bugloos\LaravelLocalization\Models\Label;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Translation\FileLoader;

class Loader extends FileLoader
{
    public function load($locale, $group, $namespace = null): array
    {
        $lines = parent::load($locale, $group, $namespace);

        if (! count($lines)) {
            return $this->loadFromDB($locale, $group);
        }

        return $lines;
    }

    protected function loadFromDB($locale, $group)
    {
        return Label::query()
            ->whereRelation('category', 'name', $group)
            ->with(
                'translation', static function (Relation $query) use ($locale) {
                    $query->whereRelation('locale', 'locale', $locale);
                })
            ->get()
            ->pluck('translation', 'key')
            ->toArray();
    }
}
