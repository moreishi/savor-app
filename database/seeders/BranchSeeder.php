<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            ['brand' => 'osave',       'name' => "O!Save Liloan",      'city' => 'Liloan'],
            ['brand' => 'osave',       'name' => "O!Save Consolacion", 'city' => 'Consolacion'],
            ['brand' => 'robinsons',   'name' => 'Robinsons Galleria',  'city' => 'Cebu City'],
            ['brand' => 'robinsons',   'name' => 'Robinsons Fuente',    'city' => 'Cebu City'],
            ['brand' => 'easymart',    'name' => 'Easymart Talisay',    'city' => 'Talisay'],
            ['brand' => 'easymart',    'name' => 'Easymart Mandaue',    'city' => 'Mandaue'],
            ['brand' => 'shopwise',    'name' => 'Shopwise Cebu',       'city' => 'Cebu City'],
            ['brand' => 'shopwise',    'name' => 'Shopwise Lapu-Lapu',  'city' => 'Lapu-Lapu'],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
