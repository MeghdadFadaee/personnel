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
        UserSeeder::crateAdmin();

        $answer = $this->command->choice('do you want create fake data?', [1 => 'Yes', 2 => 'No'], 2);
        if ($answer == 'Yes') {
            $this->call([
                UserSeeder::class,
                EmployerSeeder::class,
                ProjectSeeder::class,
            ]);
        }
    }
}
