<?php

namespace Bugloos\LaravelLocalization;

use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Models\Label;
use Bugloos\LaravelLocalization\Models\Language;
use Illuminate\Translation\FileLoader;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

class Loader extends FileLoader
{
    public function load($locale, $group, $namespace = null):array
    {
        $lines = parent::load($locale, $group, $namespace);

        if (!count($lines)) {
            return $this->loadFromDB($locale, $group);
        }

        return $lines;
    }

    protected function loadFromDB($locale, $group)
    {
        return Label::query()
            ->whereRelation('category', 'name', $group)
            ->with([
                'translations' => function (Relation $query) use ($locale) {
                    $query->getQuery()->whereRelation('locale', 'locale', $locale);
                }
            ])
            ->get()
            ->map(function ($label) {
                return array_merge(
                    $label->toArray(),
                    ['translations' => Arr::get($label->translations->first(), 'text')]
                );
            })
            ->pluck('translations', 'key')
            ->toArray();
    }
}
