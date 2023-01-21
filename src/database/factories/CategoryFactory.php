<?php

namespace Bugloos\LaravelLocalization\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = \Bugloos\LaravelLocalization\Models\Category::class;

    public function definition()
    {
        return [
            'name' => fake()->unique()->word()
        ];
    }
}
