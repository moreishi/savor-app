<?php

namespace Database\Seeders;

use App\Models\BranchPrice;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('data/branch_prices.json');
        if (!file_exists($jsonPath)) {
            $this->command->error('Price data file not found: ' . $jsonPath);
            return;
        }

        $prices = json_decode(file_get_contents($jsonPath), true);
        if (empty($prices)) {
            $this->command->error('Price data file is empty or invalid.');
            return;
        }

        // Chunked insert for performance
        foreach (array_chunk($prices, 100) as $chunk) {
            BranchPrice::insert($chunk);
        }

        $this->command->info('Seeded ' . count($prices) . ' branch prices.');
    }
}
