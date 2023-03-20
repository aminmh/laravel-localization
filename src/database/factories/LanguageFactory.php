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
        return [
            'locale' => 'en',
            'name' => 'English'
        ];
    }

    public function random(): static
    {
        return $this->state(function () {
            $index = $this->faker->numberBetween(0, count(LanguageSeeder::languages()));
            return LanguageSeeder::languages()[$index];
        });
    }
}
