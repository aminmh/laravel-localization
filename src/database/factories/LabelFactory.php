<?php

namespace Bugloos\LaravelLocalization\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LabelFactory extends Factory
{
    protected $model = \Bugloos\LaravelLocalization\Models\Label::class;

    public function definition()
    {
        return [
            'key' => fake()->word()
        ];
    }
}
