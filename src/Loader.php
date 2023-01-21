<?php

namespace Bugloos\LaravelLocalization;

use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Models\Label;
use Bugloos\LaravelLocalization\Models\Language;
use Illuminate\Translation\FileLoader;
use Illuminate\Database\Eloquent\Relations\Relation;

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
        $category = Category::query()->firstWhere('name', $group);

        if ($category) {

            $labels = Label::query()
                ->whereRelation('category', 'id', $category->id)
                ->with([
                    'translations' => function (Relation $query) use ($locale) {
                        $query->getQuery()->whereRelation('locale', 'locale', $locale);
                    }
                ]);
        }
    }
}
