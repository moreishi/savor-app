<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\PromoHighlight;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PromoHighlightSeeder extends Seeder
{
    public function run(): void
    {
        $branchIds = Branch::pluck('id')->toArray();
        $ingredientIds = [19, 20, 44, 46, 52, 53, 58, 61]; // Garlic, Onion, Rice, Cooking Oil, Soy Sauce, Vinegar, Sugar, Frying Oil

        $promos = [
            [
                'branch_id'     => $branchIds[array_rand($branchIds)],
                'ingredient_id' => 19, // Garlic
                'promo_text'    => 'Bawang Pandagdag Saya!',
                'valid_from'    => Carbon::today()->subDays(5),
                'valid_until'   => Carbon::today()->addDays(12),
                'is_active'     => true,
            ],
            [
                'branch_id'     => $branchIds[array_rand($branchIds)],
                'ingredient_id' => 20, // Onion
                'promo_text'    => 'Fiesta Pack — Sibuyas',
                'valid_from'    => Carbon::today()->subDays(2),
                'valid_until'   => Carbon::today()->addDays(18),
                'is_active'     => true,
            ],
            [
                'branch_id'     => $branchIds[array_rand($branchIds)],
                'ingredient_id' => 46, // Cooking Oil
                'promo_text'    => 'Holy Week Saver',
                'valid_from'    => Carbon::today()->subDays(1),
                'valid_until'   => Carbon::today()->addDays(8),
                'is_active'     => true,
            ],
            [
                'branch_id'     => $branchIds[array_rand($branchIds)],
                'ingredient_id' => 44, // White Rice
                'promo_text'    => 'Sack Special — Rice',
                'valid_from'    => Carbon::today()->subDays(3),
                'valid_until'   => Carbon::today()->addDays(20),
                'is_active'     => true,
            ],
            [
                'branch_id'     => $branchIds[array_rand($branchIds)],
                'ingredient_id' => 52, // Soy Sauce
                'promo_text'    => 'Datu Puti Deal',
                'valid_from'    => Carbon::today()->subDays(7),
                'valid_until'   => Carbon::today()->addDays(25),
                'is_active'     => true,
            ],
            [
                'branch_id'     => $branchIds[array_rand($branchIds)],
                'ingredient_id' => 53, // Vinegar
                'promo_text'    => 'Silver Swan Sulit',
                'valid_from'    => Carbon::today()->subDays(4),
                'valid_until'   => Carbon::today()->addDays(10),
                'is_active'     => true,
            ],
            [
                'branch_id'     => $branchIds[array_rand($branchIds)],
                'ingredient_id' => 58, // Sugar
                'promo_text'    => 'Pandesal Combo',
                'valid_from'    => Carbon::today()->subDays(6),
                'valid_until'   => Carbon::today()->addDays(15),
                'is_active'     => true,
            ],
            [
                'branch_id'     => $branchIds[array_rand($branchIds)],
                'ingredient_id' => 61, // Frying Oil
                'promo_text'    => 'Lutong Bahay Offer',
                'valid_from'    => Carbon::today(),
                'valid_until'   => Carbon::today()->addDays(14),
                'is_active'     => true,
            ],
        ];

        foreach ($promos as $promo) {
            PromoHighlight::create($promo);
        }

        $this->command->info('Seeded ' . count($promos) . ' promo highlights.');
    }
}
