<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Petani;

class PetaniSeeder extends Seeder
{
    public function run(): void
    {
        // Provinsi ID: 2 = Jawa Tengah
        // Kabupaten: 6=Kota Surakarta, 7=Kab.Boyolali, 8=Kab.Sukoharjo,
        //            9=Kab.Karanganyar, 10=Kab.Wonogiri, 11=Kab.Sragen, 12=Kab.Klaten

        $petaniData = [
            // ── Kota Surakarta ─────────────────────────────────────────────
            [
                'tanggal'           => '2026-01-05',
                'nik'               => '3372010101900001',
                'nama'              => 'Budi Santoso',
                'alamat'            => 'Jl. Slamet Riyadi No. 12, Laweyan',
                'provinsi_id'       => 2,
                'kabupaten_id'      => 6,
                'no_telepon'        => '081234567801',
                'bank'              => 'BRI',
                'no_rekening'       => '1234-5678-9001',
                'luas_lahan'        => 1.50,
                'alamat_lahan'      => 'Desa Pajang, Laweyan, Surakarta',
                'potensi_panen'     => 4500.00,
                'komoditi'          => 'Gabah',
                'status_verifikasi' => 'disetujui',
            ],
            [
                'tanggal'           => '2026-01-08',
                'nik'               => '3372010201900002',
                'nama'              => 'Siti Aminah',
                'alamat'            => 'Jl. Veteran No. 45, Serengan',
                'provinsi_id'       => 2,
                'kabupaten_id'      => 6,
                'no_telepon'        => '081234567802',
                'bank'              => 'BNI',
                'no_rekening'       => '1234-5678-9002',
                'luas_lahan'        => 0.75,
                'alamat_lahan'      => 'Desa Joyontakan, Serengan, Surakarta',
                'potensi_panen'     => 2250.00,
                'komoditi'          => 'Gabah',
                'status_verifikasi' => 'disetujui',
            ],

            // ── Kab. Boyolali ──────────────────────────────────────────────
            [
                'tanggal'           => '2026-01-12',
                'nik'               => '3309010101900003',
                'nama'              => 'Ahmad Fauzi',
                'alamat'            => 'Desa Nogosari RT 02/04, Boyolali',
                'provinsi_id'       => 2,
                'kabupaten_id'      => 7,
                'no_telepon'        => '081234567803',
                'bank'              => 'BRI',
                'no_rekening'       => '1234-5678-9003',
                'luas_lahan'        => 2.00,
                'alamat_lahan'      => 'Desa Nogosari, Boyolali',
                'potensi_panen'     => 6000.00,
                'komoditi'          => 'Gabah',
                'status_verifikasi' => 'disetujui',
            ],
            [
                'tanggal'           => '2026-01-15',
                'nik'               => '3309010201900004',
                'nama'              => 'Dwi Rahayu',
                'alamat'            => 'Jl. Pandanaran No. 7, Boyolali',
                'provinsi_id'       => 2,
                'kabupaten_id'      => 7,
                'no_telepon'        => '081234567804',
                'bank'              => 'Mandiri',
                'no_rekening'       => '1234-5678-9004',
                'luas_lahan'        => 1.25,
                'alamat_lahan'      => 'Desa Winong, Boyolali',
                'potensi_panen'     => 3750.00,
                'komoditi'          => 'Jagung',
                'status_verifikasi' => 'pending',
            ],

            // ── Kab. Sukoharjo ─────────────────────────────────────────────
            [
                'tanggal'           => '2026-01-20',
                'nik'               => '3311010101900005',
                'nama'              => 'Eko Prasetyo',
                'alamat'            => 'Desa Grogol RT 01/02, Sukoharjo',
                'provinsi_id'       => 2,
                'kabupaten_id'      => 8,
                'no_telepon'        => '081234567805',
                'bank'              => 'BRI',
                'no_rekening'       => '1234-5678-9005',
                'luas_lahan'        => 3.00,
                'alamat_lahan'      => 'Desa Grogol, Sukoharjo',
                'potensi_panen'     => 9000.00,
                'komoditi'          => 'Gabah',
                'status_verifikasi' => 'disetujui',
            ],
            [
                'tanggal'           => '2026-02-01',
                'nik'               => '3311010201900006',
                'nama'              => 'Fitri Handayani',
                'alamat'            => 'Jl. Jendral Sudirman No. 22, Sukoharjo',
                'provinsi_id'       => 2,
                'kabupaten_id'      => 8,
                'no_telepon'        => '081234567806',
                'bank'              => 'BNI',
                'no_rekening'       => '1234-5678-9006',
                'luas_lahan'        => 1.75,
                'alamat_lahan'      => 'Desa Kartasura, Sukoharjo',
                'potensi_panen'     => 5250.00,
                'komoditi'          => 'Gabah',
                'status_verifikasi' => 'pending',
            ],

            // ── Kab. Karanganyar ───────────────────────────────────────────
            [
                'tanggal'           => '2026-02-05',
                'nik'               => '3313010101900007',
                'nama'              => 'Gunawan Hartono',
                'alamat'            => 'Desa Matesih RT 03/05, Karanganyar',
                'provinsi_id'       => 2,
                'kabupaten_id'      => 9,
                'no_telepon'        => '081234567807',
                'bank'              => 'BRI',
                'no_rekening'       => '1234-5678-9007',
                'luas_lahan'        => 2.50,
                'alamat_lahan'      => 'Desa Matesih, Karanganyar',
                'potensi_panen'     => 7500.00,
                'komoditi'          => 'Gabah',
                'status_verifikasi' => 'disetujui',
            ],
            [
                'tanggal'           => '2026-02-10',
                'nik'               => '3313010201900008',
                'nama'              => 'Heni Lestari',
                'alamat'            => 'Jl. Lawu No. 15, Karanganyar',
                'provinsi_id'       => 2,
                'kabupaten_id'      => 9,
                'no_telepon'        => '081234567808',
                'bank'              => 'Mandiri',
                'no_rekening'       => '1234-5678-9008',
                'luas_lahan'        => 1.00,
                'alamat_lahan'      => 'Desa Karangpandan, Karanganyar',
                'potensi_panen'     => 3000.00,
                'komoditi'          => 'Jagung',
                'status_verifikasi' => 'ditolak',
                'catatan_verifikasi'=> 'Foto KTP tidak jelas, mohon upload ulang',
            ],

            // ── Kab. Wonogiri ──────────────────────────────────────────────
            [
                'tanggal'           => '2026-02-14',
                'nik'               => '3312010101900009',
                'nama'              => 'Irwan Setiawan',
                'alamat'            => 'Desa Pracimantoro RT 01/03, Wonogiri',
                'provinsi_id'       => 2,
                'kabupaten_id'      => 10,
                'no_telepon'        => '081234567809',
                'bank'              => 'BRI',
                'no_rekening'       => '1234-5678-9009',
                'luas_lahan'        => 4.00,
                'alamat_lahan'      => 'Desa Pracimantoro, Wonogiri',
                'potensi_panen'     => 12000.00,
                'komoditi'          => 'Gabah',
                'status_verifikasi' => 'disetujui',
            ],
            [
                'tanggal'           => '2026-02-18',
                'nik'               => '3312010201900010',
                'nama'              => 'Joko Susilo',
                'alamat'            => 'Jl. Diponegoro No. 8, Wonogiri',
                'provinsi_id'       => 2,
                'kabupaten_id'      => 10,
                'no_telepon'        => '081234567810',
                'bank'              => 'BNI',
                'no_rekening'       => '1234-5678-9010',
                'luas_lahan'        => 2.25,
                'alamat_lahan'      => 'Desa Baturetno, Wonogiri',
                'potensi_panen'     => 6750.00,
                'komoditi'          => 'Gabah',
                'status_verifikasi' => 'pending',
            ],

            // ── Kab. Sragen ────────────────────────────────────────────────
            [
                'tanggal'           => '2026-02-20',
                'nik'               => '3314010101900011',
                'nama'              => 'Kartini Wulandari',
                'alamat'            => 'Desa Masaran RT 02/04, Sragen',
                'provinsi_id'       => 2,
                'kabupaten_id'      => 11,
                'no_telepon'        => '081234567811',
                'bank'              => 'BRI',
                'no_rekening'       => '1234-5678-9011',
                'luas_lahan'        => 1.80,
                'alamat_lahan'      => 'Desa Masaran, Sragen',
                'potensi_panen'     => 5400.00,
                'komoditi'          => 'Gabah',
                'status_verifikasi' => 'disetujui',
            ],
            [
                'tanggal'           => '2026-02-25',
                'nik'               => '3314010201900012',
                'nama'              => 'Lilik Purwanto',
                'alamat'            => 'Jl. Raya Sragen No. 30, Sragen',
                'provinsi_id'       => 2,
                'kabupaten_id'      => 11,
                'no_telepon'        => '081234567812',
                'bank'              => 'Mandiri',
                'no_rekening'       => '1234-5678-9012',
                'luas_lahan'        => 3.50,
                'alamat_lahan'      => 'Desa Kedawung, Sragen',
                'potensi_panen'     => 10500.00,
                'komoditi'          => 'Jagung',
                'status_verifikasi' => 'pending',
            ],

            // ── Kab. Klaten ────────────────────────────────────────────────
            [
                'tanggal'           => '2026-03-01',
                'nik'               => '3310010101900013',
                'nama'              => 'Mulyono Wibowo',
                'alamat'            => 'Desa Ceper RT 01/02, Klaten',
                'provinsi_id'       => 2,
                'kabupaten_id'      => 12,
                'no_telepon'        => '081234567813',
                'bank'              => 'BRI',
                'no_rekening'       => '1234-5678-9013',
                'luas_lahan'        => 2.00,
                'alamat_lahan'      => 'Desa Ceper, Klaten',
                'potensi_panen'     => 6000.00,
                'komoditi'          => 'Gabah',
                'status_verifikasi' => 'disetujui',
            ],
            [
                'tanggal'           => '2026-03-03',
                'nik'               => '3310010201900014',
                'nama'              => 'Nanik Rahmawati',
                'alamat'            => 'Jl. Pemuda No. 11, Klaten',
                'provinsi_id'       => 2,
                'kabupaten_id'      => 12,
                'no_telepon'        => '081234567814',
                'bank'              => 'BNI',
                'no_rekening'       => '1234-5678-9014',
                'luas_lahan'        => 1.40,
                'alamat_lahan'      => 'Desa Prambanan, Klaten',
                'potensi_panen'     => 4200.00,
                'komoditi'          => 'Gabah',
                'status_verifikasi' => 'pending',
            ],
            [
                'tanggal'           => '2026-03-04',
                'nik'               => '3310010301900015',
                'nama'              => 'Oki Dermawan',
                'alamat'            => 'Desa Delanggu RT 04/06, Klaten',
                'provinsi_id'       => 2,
                'kabupaten_id'      => 12,
                'no_telepon'        => '081234567815',
                'bank'              => 'Mandiri',
                'no_rekening'       => '1234-5678-9015',
                'luas_lahan'        => 0.90,
                'alamat_lahan'      => 'Desa Delanggu, Klaten',
                'potensi_panen'     => 2700.00,
                'komoditi'          => 'Jagung',
                'status_verifikasi' => 'pending',
            ],
        ];

        foreach ($petaniData as $data) {
            // Skip jika NIK sudah ada
            if (Petani::where('nik', $data['nik'])->exists()) {
                continue;
            }

            Petani::create(array_merge($data, [
                'catatan_verifikasi' => $data['catatan_verifikasi'] ?? null,
                'verified_at'        => $data['status_verifikasi'] !== 'pending'
                    ? now()->subDays(rand(1, 15))
                    : null,
            ]));
        }

        $this->command->info('✅ PetaniSeeder: 15 data petani berhasil dibuat.');
    }
}
