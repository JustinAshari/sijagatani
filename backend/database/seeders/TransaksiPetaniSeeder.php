<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Petani;
use App\Models\TransaksiPetani;
use Carbon\Carbon;

class TransaksiPetaniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $petani = Petani::all();

        if ($petani->isEmpty()) {
            return;
        }

        $statuses = ['pending', 'sukses', 'gagal'];

        foreach ($petani as $p) {
            // Generate 1-3 transactions per farmer
            $numTransactions = rand(1, 3);

            for ($i = 0; $i < $numTransactions; $i++) {
                // Determine price per kg based on komoditi
                $pricePerKg = match ($p->komoditi) {
                    'Beras' => rand(12000, 14000),
                    'Jagung' => rand(4500, 5500),
                    default => rand(6000, 7500), // Gabah
                };

                // Volume between 100 kg to 3000 kg
                $volume = rand(100, 300) * 10; 
                $nominal = $volume * $pricePerKg;

                // Random date in the last 6 months
                $date = Carbon::now()->subDays(rand(1, 180))->format('Y-m-d');

                TransaksiPetani::create([
                    'petani_id' => $p->id,
                    'tanggal_transaksi' => $date,
                    'volume_kg' => $volume,
                    'nominal' => $nominal,
                    'status_transaksi' => $statuses[array_rand($statuses)],
                ]);
            }
        }
    }
}
