<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create superadmin user
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@sijagatani.com',
            'password' => Hash::make('admin123'),
            'role' => 'superadmin',
        ]);

        // Create regular user
        User::create([
            'name' => 'User Demo',
            'email' => 'user@sijagatani.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
        ]);
    }
}
