<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use App\Models\Employer;
use Illuminate\Database\Seeder;

class EmployerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employers = Employer::factory(10)->create();

        User::all()->each(function (User $user) use ($employers) {
            $user->employers()->attach(
                $employers->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}
