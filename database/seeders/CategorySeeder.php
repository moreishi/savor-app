<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Meat',          'slug' => 'meat',        'icon' => '🍗', 'sort_order' => 1],
            ['name' => 'Seafood',       'slug' => 'seafood',     'icon' => '🐟', 'sort_order' => 2],
            ['name' => 'Vegetables',    'slug' => 'vegetables',  'icon' => '🥬', 'sort_order' => 3],
            ['name' => 'Dairy',         'slug' => 'dairy',       'icon' => '🥛', 'sort_order' => 4],
            ['name' => 'Grains',        'slug' => 'grains',      'icon' => '🍚', 'sort_order' => 5],
            ['name' => 'Noodles',       'slug' => 'noodles',     'icon' => '🍜', 'sort_order' => 6],
            ['name' => 'Pantry',        'slug' => 'pantry',      'icon' => '🧂', 'sort_order' => 7],
            ['name' => 'Dessert',       'slug' => 'dessert',     'icon' => '🍰', 'sort_order' => 8],
            ['name' => 'Soup',          'slug' => 'soup',        'icon' => '🥣', 'sort_order' => 9],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
