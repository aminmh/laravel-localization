<?php

return [
    'default_locale' => 'en',
    'fallback_locale' => 'en',

    'tables' => [
        \Bugloos\LaravelLocalization\Models\Label::class => 'labels',
        \Bugloos\LaravelLocalization\Models\Category::class => 'categories',
        \Bugloos\LaravelLocalization\Models\Language::class => 'languages',
        \Bugloos\LaravelLocalization\Models\Translation::class => 'translations',
    ],
];
