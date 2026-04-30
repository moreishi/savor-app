<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'slug');

        $ingredients = [
            // Meat (1)
            ['category_id' => $categories['meat'], 'ingredients' => [
                ['name' => 'Chicken Thigh',       'default_unit' => 'kg'],
                ['name' => 'Chicken Breast',      'default_unit' => 'kg'],
                ['name' => 'Pork Belly',          'default_unit' => 'kg'],
                ['name' => 'Pork Loin',           'default_unit' => 'kg'],
                ['name' => 'Ground Pork',         'default_unit' => 'kg'],
                ['name' => 'Beef Bulalo',         'default_unit' => 'kg'],
                ['name' => 'Beef Tapa',           'default_unit' => 'kg'],
                ['name' => 'Beef Kaldereta Cut',  'default_unit' => 'kg'],
                ['name' => 'Beef Mechado Cut',    'default_unit' => 'kg'],
                ['name' => 'Beef Nilaga Cut',     'default_unit' => 'kg'],
                ['name' => 'Pork Sisig',          'default_unit' => 'kg'],
                ['name' => 'Chicken Wings',       'default_unit' => 'kg'],
                ['name' => 'Ground Beef',         'default_unit' => 'kg'],
            ]],
            // Seafood (2)
            ['category_id' => $categories['seafood'], 'ingredients' => [
                ['name' => 'Bangus (Milkfish)',    'default_unit' => 'kg'],
                ['name' => 'Squid (Pusit)',        'default_unit' => 'kg'],
                ['name' => 'Tilapia',              'default_unit' => 'kg'],
                ['name' => 'Shrimp',               'default_unit' => 'kg'],
                ['name' => 'Tahong (Mussels)',     'default_unit' => 'kg'],
            ]],
            // Vegetables (3)
            ['category_id' => $categories['vegetables'], 'ingredients' => [
                ['name' => 'Garlic',               'default_unit' => 'kg'],
                ['name' => 'Onion',                'default_unit' => 'kg'],
                ['name' => 'Tomato',               'default_unit' => 'kg'],
                ['name' => 'Ginger',               'default_unit' => 'kg'],
                ['name' => 'Cabbage',              'default_unit' => 'kg'],
                ['name' => 'Kangkong (Water Spinach)', 'default_unit' => 'bundle'],
                ['name' => 'Pechay (Bok Choy)',    'default_unit' => 'bundle'],
                ['name' => 'Eggplant',             'default_unit' => 'kg'],
                ['name' => 'Ampalaya (Bitter Gourd)', 'default_unit' => 'kg'],
                ['name' => 'Sitaw (String Beans)', 'default_unit' => 'kg'],
                ['name' => 'Okra',                 'default_unit' => 'kg'],
                ['name' => 'Kalabasa (Squash)',    'default_unit' => 'kg'],
                ['name' => 'Dahon ng Sili (Chili Leaves)', 'default_unit' => 'bundle'],
                ['name' => 'Monggo Beans',         'default_unit' => 'kg'],
                ['name' => 'Sayote (Chayote)',     'default_unit' => 'kg'],
                ['name' => 'Carrot',               'default_unit' => 'kg'],
                ['name' => 'Green Bell Pepper',    'default_unit' => 'pcs'],
                ['name' => 'Lemon Grass',          'default_unit' => 'bundle'],
                ['name' => 'Coconut (Gata)',       'default_unit' => 'pcs'],
                ['name' => 'Potato',               'default_unit' => 'kg'],
            ]],
            // Dairy (4)
            ['category_id' => $categories['dairy'], 'ingredients' => [
                ['name' => 'Evaporated Milk',      'default_unit' => 'can'],
                ['name' => 'Condensed Milk',       'default_unit' => 'can'],
                ['name' => 'Cheddar Cheese',       'default_unit' => 'kg'],
                ['name' => 'Eggs',                 'default_unit' => 'dozen'],
                ['name' => 'Butter',               'default_unit' => 'kg'],
            ]],
            // Grains (5)
            ['category_id' => $categories['grains'], 'ingredients' => [
                ['name' => 'White Rice',           'default_unit' => 'kg'],
                ['name' => 'Glutinous Rice (Malagkit)', 'default_unit' => 'kg'],
                ['name' => 'Cooking Oil',          'default_unit' => 'L'],
            ]],
            // Noodles (6)
            ['category_id' => $categories['noodles'], 'ingredients' => [
                ['name' => 'Pancit Canton Noodles', 'default_unit' => 'pack'],
                ['name' => 'Pancit Bihon (Rice Noodles)', 'default_unit' => 'pack'],
                ['name' => 'Sotanghon (Glass Noodles)', 'default_unit' => 'pack'],
                ['name' => 'Spaghetti Pasta',      'default_unit' => 'pack'],
                ['name' => 'Egg Noodles (Miki)',   'default_unit' => 'kg'],
            ]],
            // Pantry (7)
            ['category_id' => $categories['pantry'], 'ingredients' => [
                ['name' => 'Soy Sauce (Toyo)',     'default_unit' => 'bottle'],
                ['name' => 'Vinegar (Suka)',       'default_unit' => 'bottle'],
                ['name' => 'Fish Sauce (Patis)',   'default_unit' => 'bottle'],
                ['name' => 'Salt',                 'default_unit' => 'kg'],
                ['name' => 'Ground Black Pepper',  'default_unit' => 'pack'],
                ['name' => 'Bay Leaf (Laurel)',    'default_unit' => 'pack'],
                ['name' => 'Sugar (White)',        'default_unit' => 'kg'],
                ['name' => 'Brown Sugar',          'default_unit' => 'kg'],
                ['name' => 'Cornstarch',           'default_unit' => 'kg'],
                ['name' => 'Cooking Oil (Frying)', 'default_unit' => 'L'],
                ['name' => 'Caldereta Sauce Mix',  'default_unit' => 'pack'],
                ['name' => 'Sinigang Mix',         'default_unit' => 'pack'],
            ]],
            // Dessert (8)
            ['category_id' => $categories['dessert'], 'ingredients' => [
                ['name' => 'Ripe Plantain (Saba)',  'default_unit' => 'kg'],
                ['name' => 'Jackfruit (Langka)',    'default_unit' => 'kg'],
                ['name' => 'Purple Yam (Ube)',      'default_unit' => 'kg'],
                ['name' => 'Coconut Milk (Kakang Gata)', 'default_unit' => 'can'],
                ['name' => 'Vanilla Extract',       'default_unit' => 'bottle'],
                ['name' => 'All-Purpose Flour',     'default_unit' => 'kg'],
                ['name' => 'Breadcrumbs (Panko)',   'default_unit' => 'kg'],
            ]],
        ];

        foreach ($ingredients as $group) {
            foreach ($group['ingredients'] as $ingredient) {
                Ingredient::create([
                    'category_id'  => $group['category_id'],
                    'name'         => $ingredient['name'],
                    'slug'         => Str::slug($ingredient['name']),
                    'default_unit' => $ingredient['default_unit'],
                ]);
            }
        }
    }
}
