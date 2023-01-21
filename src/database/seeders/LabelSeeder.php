<?php

namespace Bugloos\LaravelLocalization\database\seeders;

use Bugloos\LaravelLocalization\database\factories\CategoryFactory;
use Bugloos\LaravelLocalization\database\factories\LabelFactory;
use Bugloos\LaravelLocalization\Models\Category;
use Bugloos\LaravelLocalization\Models\Label;
use Illuminate\Database\Seeder;

class LabelSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();

        if (count($categories)) {
            foreach ($categories as $category) {
                LabelFactory::createWithRealNames(10, ['category_id' => $category->id]);
            }
        }
    }

    private function createWithFakeNames(int $count = 0, array $attributes = [])
    {
        $factory = Label::factory();

        if ($count) {
            return $factory->count($count)->create($attributes);
        }

        return $factory->create($attributes);
    }
}
