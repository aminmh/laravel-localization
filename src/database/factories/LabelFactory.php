<?php

namespace Bugloos\LaravelLocalization\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

class LabelFactory extends Factory
{
    protected $model = \Bugloos\LaravelLocalization\Models\Label::class;

    public function definition()
    {
        return [
            'key' => fake()->word(),
        ];
    }

    public function genuine(): static
    {
        if ($this->count === 1) {
            return $this->one();
        }

        return $this->sequence(function (Sequence $sequence) {
            return [
                'key' => static::labels()[$sequence->index + $this->faker->numberBetween($sequence->index, count(static::labels()) - 1)]
            ];
        });
    }

    private static function labels(): array
    {
        return [
            'car',
            'home',
            'job',
            'credit card',
            'bank',
            'mother',
            'family',
            'plan',
            'map',
            'world',
            'computer',
            'phone',
            'error',
            'label',
            'doctor',
            'teacher',
            'lesson',
            'network',
            'internet',
            'process',
            'drag',
            'love',
            'friend',
            'realationship',
            'dog',
            'engineer',
        ];
    }

    private function one(): static
    {
        $labels = static::labels();

        shuffle($labels);

        $index = $this->faker->numberBetween(0, count($labels));

        $label = $labels[$index];

        return $this->state(function () use ($label) {
            return [
                'key' => $label
            ];
        });
    }
}
