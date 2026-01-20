<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleBasedUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin user
        User::firstOrCreate(
            ['email' => 'superadmin@sijagatani.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('superadmin123'),
                'role' => 'superadmin',
            ]
        );

        // Update existing users
        $existingAdmin = User::where('email', 'admin@sijagatani.com')->first();
        if ($existingAdmin) {
            $existingAdmin->update([
                'name' => 'Administrator',
                'role' => 'admin',
                'password' => Hash::make('admin123')
            ]);
        } else {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@sijagatani.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);
        }

        $existingUser = User::where('email', 'user@sijagatani.com')->first();
        if ($existingUser) {
            $existingUser->delete();
        }

        // Create Petani user (Admin Lapangan Bulog)
        User::firstOrCreate(
            ['email' => 'petani@sijagatani.com'],
            [
                'name' => 'Admin Lapangan Bulog',
                'password' => Hash::make('petani123'),
                'role' => 'petani',
            ]
        );

        // Create Penggilingan user (Admin Penggilingan)
        User::firstOrCreate(
            ['email' => 'penggilingan@sijagatani.com'],
            [
                'name' => 'Admin Penggilingan Makloon',
                'password' => Hash::make('penggilingan123'),
                'role' => 'penggilingan',
            ]
        );
    }
}
