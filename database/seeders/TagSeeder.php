<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Budget Meals',
            '30-Minute Meals',
            'Keto-Friendly',
            'Party Food',
            'Quick Lunch',
            'Family Size',
            'Meal Prep',
            'Filipino Classics',
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'name' => $tag,
                'slug' => Str::slug($tag),
            ]);
        }
    }
}
