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

        $statuses = ['belum', 'sudah'];

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

                $statusTransaksi = $statuses[array_rand($statuses)];
                $statusVerifikasi = match ($statusTransaksi) {
                    'sudah' => 'disetujui',
                    default => rand(0, 1) ? 'pending' : 'ditolak',
                };
                $catatanVerifikasi = match ($statusVerifikasi) {
                    'disetujui' => 'Transaksi valid dan sesuai dokumen.',
                    'ditolak' => 'Dokumen pendukung tidak lengkap.',
                    default => null,
                };
                $verifiedAt = $statusVerifikasi !== 'pending' ? Carbon::parse($date)->addHours(rand(1, 24)) : null;
                $verifiedBy = null;
                if ($statusVerifikasi !== 'pending') {
                    $verifiedBy = \App\Models\User::where('role', 'admin')->first()?->id 
                        ?? \App\Models\User::first()?->id;
                }

                TransaksiPetani::create([
                    'petani_id' => $p->id,
                    'komoditas' => $p->komoditi ?? 'Gabah',
                    'tanggal_transaksi' => $date,
                    'volume_kg' => $volume,
                    'harga_per_kg' => $pricePerKg,
                    'nominal' => $nominal,
                    'status_transaksi' => $statusTransaksi,
                    'status_verifikasi' => $statusVerifikasi,
                    'catatan_verifikasi' => $catatanVerifikasi,
                    'verified_at' => $verifiedAt,
                    'verified_by' => $verifiedBy,
                    'bank' => $p->bank,
                    'no_rekening' => $p->no_rekening,
                ]);
            }
        }
    }
}
