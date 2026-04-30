<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SavorDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BranchSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            IngredientSeeder::class,
            RecipeSeeder::class,
            PriceSeeder::class,
        ]);
    }
}
