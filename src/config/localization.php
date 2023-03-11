<?php

return [
    'default_locale' => 'en',
    'fallback_locale' => 'en',

    'flag' => [
        'path' => null, //Absolute flag files path
        'mime_type' => 'png',
    ],

    'extract' => [
        'export_path' => storage_path('app/public'),
        'extractors' => [
            'php' => \Bugloos\LaravelLocalization\Extractor\ExtractorTypes\ArrayExtractor::class,
            'json' => \Bugloos\LaravelLocalization\Extractor\ExtractorTypes\JsonExtractor::class
        ]
    ],

    'migrate' => [
        'migrators' => [
            'yaml' => [
//                'migrator' => 'some class',
//                'loader' => 'some class'
            ]
        ]
    ],


    'tables' => [
        \Bugloos\LaravelLocalization\Models\Label::class => 'labels',
        \Bugloos\LaravelLocalization\Models\Category::class => 'categories',
        \Bugloos\LaravelLocalization\Models\Language::class => 'languages',
        \Bugloos\LaravelLocalization\Models\Translation::class => 'translations',
        \Bugloos\LaravelLocalization\Models\Country::class => 'countries',
    ],
];
