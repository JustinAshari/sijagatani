<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Super Admin',
                'username' => 'admin',
                'password' => Hash::make('adminada'),
                'role'     => 'superadmin',
            ],
            [
                'name'     => 'Admin Sijagatani',
                'username' => 'adminsijaga',
                'password' => Hash::make('adminada'),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Penggilingan 1',
                'username' => 'penggilingan1',
                'password' => Hash::make('adminada'),
                'role'     => 'penggilingan',
            ],
            [
                'name'     => 'Tim Jemput Pangan Test',
                'username' => 'tjptest',
                'password' => Hash::make('adminada'),
                'role'     => 'lapangan',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
