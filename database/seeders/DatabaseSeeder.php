<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            // Future seeders will go here:
            // EmissionSeeder::class,
            // PostSeeder::class,
        ]);
    }
}