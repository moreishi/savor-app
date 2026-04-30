<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user for Breeze login
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@savor.ph',
            'password' => bcrypt('password'),
        ]);

        // Seed Savor data
        $this->call(SavorDatabaseSeeder::class);
    }
}
