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

    public static function createWithRealNames(int $count, array $attributes = [])
    {
        if ($count <= count(static::realNames())) {
            $records = [];

            $labels = static::realNames();

            shuffle($labels);

            $labels = array_slice($labels, 0, $count);

            for ($i = 0; $i < $count; $i++) {
                $records[] = array_merge(['key' => $labels[$i]], $attributes);
            }

            return (new static())
                ->state([])
                ->configure()
                ->createMany($records);
        }
    }

    private static function realNames()
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
            'engineer'
        ];
    }
}
