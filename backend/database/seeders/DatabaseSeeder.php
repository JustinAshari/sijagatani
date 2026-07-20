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
        $this->call([
            WilayahSeeder::class,
            UserSeeder::class,
            PenggilinganSeeder::class,
            PetaniSeeder::class,
            TransaksiPetaniSeeder::class,
        ]);
    }
}
