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
        UserSeeder::makeAdmin();
        $this->call([
            UserSeeder::class,
            ProjectSeeder::class,
        ]);
    }
}