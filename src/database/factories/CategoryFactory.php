<?php

namespace Bugloos\LaravelLocalization\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = \Bugloos\LaravelLocalization\Models\Category::class;

    public function definition()
    {
        return [
            'name' => fake()->unique()->word(),
        ];
    }

    public static function createWithRealNames()
    {
        return (new static())
            ->state([])
            ->configure()
            ->createMany(
                array_map(
                    fn ($category) => ['name' => $category],
                    static::realNames()
                )
            );
    }

    private static function realNames()
    {
        return [
            'global',
            'things',
            'tech',
            'financial',
            'human',
        ];
    }
}
