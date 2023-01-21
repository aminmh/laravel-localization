<?php

namespace Bugloos\LaravelLocalization\database\seeders;

use Bugloos\LaravelLocalization\database\factories\CategoryFactory;
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
                Label::factory()
                    ->count(10)
                    ->create(['category_id' => $category->id]);
            }
        }
    }
}
