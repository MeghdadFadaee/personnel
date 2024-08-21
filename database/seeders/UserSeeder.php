<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(10)->create();
    }

    public static function crateAdmin(): void
    {
        User::factory()->create([
            'first_name' => 'admin',
            'last_name' => '',
            'username' => 'admin',
            'mobile' => '',
            'email' => 'admin@admin.com',
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('admin@admin'),
            'remember_token' => Str::random(10),
        ]);
    }
}
