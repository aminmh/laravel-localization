<?php

namespace Bugloos\LaravelLocalization\Database\Factories;

use Bugloos\LaravelLocalization\database\seeders\LanguageSeeder;
use Bugloos\LaravelLocalization\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class LanguageFactory extends Factory
{
    protected $model = Language::class;

    /**
     * @inheritDoc
     */
    public function definition(): array
    {
        return LanguageSeeder::languages()[111];
    }
}
