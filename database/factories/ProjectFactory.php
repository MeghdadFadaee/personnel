<?php

namespace Database\Factories;

use App\Models\Employer;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $employerCount = Employer::query()->count();
        return [
            'employer_id' => fake()->numberBetween(0, $employerCount),
            'title' => fake('en')->jobTitle(),
            'amount' => fake()->numberBetween(0, 10),
            'fee' => round(fake()->numberBetween(10_000, 100_000), -3),
        ];
    }
}
