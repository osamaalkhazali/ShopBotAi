<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    User::create([
        'name' => 'Osama Khazali',
        'email' => 'osama@example.com',
        'password' => Hash::make('password123'),
    ]);

    User::factory()->count(5)->create(); // Add 5 random fake users too
}
}
