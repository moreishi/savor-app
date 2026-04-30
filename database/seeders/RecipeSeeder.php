<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create(['email' => 'admin@savor.ph']);
        }

        $tags = Tag::pluck('id', 'slug');
        $categories = Category::pluck('id', 'slug');
        $ingredients = Ingredient::pluck('id', 'name');

        $jsonPath = database_path('data/recipes.json');
        if (!file_exists($jsonPath)) {
            $this->command->error('Recipe data file not found: ' . $jsonPath);
            return;
        }

        $recipes = json_decode(file_get_contents($jsonPath), true);
        if (empty($recipes)) {
            $this->command->error('Recipe data file is empty or invalid.');
            return;
        }

        foreach ($recipes as $data) {
            $recipeTags = [];
            foreach ($data['tags'] as $slug) {
                if (isset($tags[$slug])) {
                    $recipeTags[] = $tags[$slug];
                }
            }

            $category = $categories[$data['category']] ?? null;

            $recipe = Recipe::create([
                'user_id' => $user->id,
                'category_id' => $category,
                'title' => $data['title'],
                'slug' => $data['slug'],
                'description' => $data['description'],
                'servings' => $data['servings'],
                'prep_time' => $data['prep_time'],
                'cook_time' => $data['cook_time'],
                'difficulty' => $data['difficulty'],
                'instructions' => $data['instructions'],
                'is_featured' => $data['is_featured'],
            ]);

            if (!empty($recipeTags)) {
                $recipe->tags()->attach($recipeTags);
            }

            $ingredientPivot = [];
            foreach ($data['ingredients'] as $idx => $item) {
                $ingredient = $ingredients[$item['name']] ?? null;
                if ($ingredient) {
                    $ingredientPivot[$ingredient] = [
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'notes' => $item['notes'] ?? null,
                        'sort_order' => $idx + 1,
                    ];
                }
            }
            if (!empty($ingredientPivot)) {
                $recipe->ingredients()->attach($ingredientPivot);
            }
        }

        $this->command->info('Seeded ' . count($recipes) . ' recipes.');
    }
}
