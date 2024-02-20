<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Factories\UserFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'Robbi Rodhiyan',
            'email' => 'robbirodhiyan@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'), // You can change 'password' to the desired password
            'remember_token' => Str::random(10),
            'role' => 'owner', // Set the desired role, e.g., 'owner'
        ]);
    }
}

