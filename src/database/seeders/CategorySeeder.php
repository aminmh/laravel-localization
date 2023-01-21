<?php

namespace Bugloos\LaravelLocalization\database\seeders;

use Bugloos\LaravelLocalization\database\factories\CategoryFactory;
use Bugloos\LaravelLocalization\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Category::factory()->count(5)->create();

        CategoryFactory::createWithRealNames();
    }
}
