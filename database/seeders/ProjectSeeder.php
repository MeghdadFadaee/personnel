<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::factory(10)->create();

        User::all()->each(function (User $user) use ($projects) {
            $user->projects()->attach(
                $projects->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}
