<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Penggilingan;
use App\Models\PenggilinganTransport;
use Illuminate\Support\Facades\Hash;

class PenggilinganSeeder extends Seeder
{
    public function run(): void
    {
        // =====================================================================
        // 3 Akun Penggilingan/Makloon beserta datanya
        // =====================================================================
        $penggilinganList = [
            [
                'user' => [
                    'name'              => 'Admin - UD Sumber Rezeki',
                    'username'          => 'penggilingan_sumberrezeki',
                    'password'          => 'makloon123',
                    'role'              => 'penggilingan',
                    'nama_penggilingan' => 'UD Sumber Rezeki',
                ],
                'data' => [
                    [
                        'tanggal_pengajuan'   => '2026-01-10',
                        'nama_petani'         => 'Budi Santoso',
                        'lokasi_makloon'      => 'Kabupaten Bantul',
                        'status_verifikasi'   => 'disetujui',
                        'transports' => [
                            ['nama_pengemudi' => 'Agus Prayogo',    'nopol' => 'AB 1234 CD', 'kuantum' => 5.250],
                            ['nama_pengemudi' => 'Slamet Riyadi',   'nopol' => 'AB 5678 EF', 'kuantum' => 4.800],
                            ['nama_pengemudi' => 'Heru Susanto',    'nopol' => 'AB 9012 GH', 'kuantum' => 6.100],
                        ],
                    ],
                    [
                        'tanggal_pengajuan'   => '2026-02-05',
                        'nama_petani'         => 'Siti Rahayu',
                        'lokasi_makloon'      => 'Kabupaten Bantul',
                        'status_verifikasi'   => 'pending',
                        'transports' => [
                            ['nama_pengemudi' => 'Doni Prasetyo',   'nopol' => 'AB 3456 IJ', 'kuantum' => 7.000],
                            ['nama_pengemudi' => 'Rudi Hartono',    'nopol' => 'AB 7890 KL', 'kuantum' => 5.500],
                        ],
                    ],
                    [
                        'tanggal_pengajuan'   => '2026-03-01',
                        'nama_petani'         => 'Joko Widodo',
                        'lokasi_makloon'      => 'Kabupaten Bantul',
                        'status_verifikasi'   => 'pending',
                        'transports' => [
                            ['nama_pengemudi' => 'Wahyu Nugroho',   'nopol' => 'AB 2345 MN', 'kuantum' => 4.200],
                        ],
                    ],
                ],
            ],
            [
                'user' => [
                    'name'              => 'Admin - Makloon Makmur Jaya',
                    'username'          => 'penggilingan_makmurajaya',
                    'password'          => 'makloon123',
                    'role'              => 'penggilingan',
                    'nama_penggilingan' => 'Makloon Makmur Jaya',
                ],
                'data' => [
                    [
                        'tanggal_pengajuan'   => '2026-01-20',
                        'nama_petani'         => 'Ahmad Fauzi',
                        'lokasi_makloon'      => 'Kabupaten Sleman',
                        'status_verifikasi'   => 'disetujui',
                        'transports' => [
                            ['nama_pengemudi' => 'Eko Prasetyo',   'nopol' => 'AB 1111 AA', 'kuantum' => 8.750],
                            ['nama_pengemudi' => 'Widodo Santoso', 'nopol' => 'AB 2222 BB', 'kuantum' => 7.300],
                            ['nama_pengemudi' => 'Purnomo Aji',    'nopol' => 'AB 3333 CC', 'kuantum' => 6.050],
                            ['nama_pengemudi' => 'Teguh Wibowo',   'nopol' => 'AB 4444 DD', 'kuantum' => 5.900],
                        ],
                    ],
                    [
                        'tanggal_pengajuan'   => '2026-02-18',
                        'nama_petani'         => 'Dewi Lestari',
                        'lokasi_makloon'      => 'Kabupaten Sleman',
                        'status_verifikasi'   => 'ditolak',
                        'catatan_verifikasi'  => 'Dokumen nota timbang tidak lengkap, mohon dilengkapi ulang',
                        'transports' => [
                            ['nama_pengemudi' => 'Bambang Irawan', 'nopol' => 'AB 5555 EE', 'kuantum' => 3.500],
                            ['nama_pengemudi' => 'Suryo Adi',      'nopol' => 'AB 6666 FF', 'kuantum' => 4.250],
                        ],
                    ],
                    [
                        'tanggal_pengajuan'   => '2026-03-03',
                        'nama_petani'         => 'Tri Handayani',
                        'lokasi_makloon'      => 'Kabupaten Sleman',
                        'status_verifikasi'   => 'pending',
                        'transports' => [
                            ['nama_pengemudi' => 'Yusuf Rahman',   'nopol' => 'AB 7777 GG', 'kuantum' => 9.000],
                            ['nama_pengemudi' => 'Andi Cahyono',   'nopol' => 'AB 8888 HH', 'kuantum' => 8.500],
                            ['nama_pengemudi' => 'Fajar Rizki',    'nopol' => 'AB 9999 II', 'kuantum' => 7.750],
                        ],
                    ],
                ],
            ],
            [
                'user' => [
                    'name'              => 'Admin - Rice Mill Bersama',
                    'username'          => 'penggilingan_ricemillbersama',
                    'password'          => 'makloon123',
                    'role'              => 'penggilingan',
                    'nama_penggilingan' => 'Rice Mill Bersama',
                ],
                'data' => [
                    [
                        'tanggal_pengajuan'   => '2026-01-15',
                        'nama_petani'         => 'Suryanto Hadi',
                        'lokasi_makloon'      => 'Kabupaten Kulon Progo',
                        'status_verifikasi'   => 'disetujui',
                        'transports' => [
                            ['nama_pengemudi' => 'Mulyono',       'nopol' => 'AB 1010 JJ', 'kuantum' => 5.000],
                            ['nama_pengemudi' => 'Suharto',       'nopol' => 'AB 2020 KK', 'kuantum' => 4.500],
                        ],
                    ],
                    [
                        'tanggal_pengajuan'   => '2026-02-10',
                        'nama_petani'         => 'Rina Wati',
                        'lokasi_makloon'      => 'Kabupaten Kulon Progo',
                        'status_verifikasi'   => 'disetujui',
                        'transports' => [
                            ['nama_pengemudi' => 'Gunawan',       'nopol' => 'AB 3030 LL', 'kuantum' => 6.800],
                            ['nama_pengemudi' => 'Hartono',       'nopol' => 'AB 4040 MM', 'kuantum' => 5.200],
                            ['nama_pengemudi' => 'Pranoto',       'nopol' => 'AB 5050 NN', 'kuantum' => 7.100],
                        ],
                    ],
                    [
                        'tanggal_pengajuan'   => '2026-02-28',
                        'nama_petani'         => 'Wawan Setiawan',
                        'lokasi_makloon'      => 'Kabupaten Kulon Progo',
                        'status_verifikasi'   => 'pending',
                        'transports' => [
                            ['nama_pengemudi' => 'Sutrisno',      'nopol' => 'AB 6060 OO', 'kuantum' => 3.750],
                            ['nama_pengemudi' => 'Parno',         'nopol' => 'AB 7070 PP', 'kuantum' => 4.000],
                            ['nama_pengemudi' => 'Darmo',         'nopol' => 'AB 8080 QQ', 'kuantum' => 5.300],
                            ['nama_pengemudi' => 'Legiman',       'nopol' => 'AB 9090 RR', 'kuantum' => 6.150],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($penggilinganList as $entry) {
            // ── Buat/update akun user penggilingan ──────────────────────────
            $user = User::firstOrCreate(
                ['username' => $entry['user']['username']],
                [
                    'name'              => $entry['user']['name'],
                    'password'          => Hash::make($entry['user']['password']),
                    'role'              => $entry['user']['role'],
                    'nama_penggilingan' => $entry['user']['nama_penggilingan'],
                ]
            );

            // Pastikan nama_penggilingan ter-set (kalau user sudah ada sebelumnya)
            $user->update([
                'nama_penggilingan' => $entry['user']['nama_penggilingan'],
                'role'              => 'penggilingan',
            ]);

            // ── Buat data penggilingan ────────────────────────────────────
            foreach ($entry['data'] as $pengData) {
                // Skip jika sudah ada (cegah duplikasi kalau seeder dijalankan ulang)
                $exists = Penggilingan::where('nama_penggilingan', $entry['user']['nama_penggilingan'])
                    ->whereDate('tanggal_pengajuan', $pengData['tanggal_pengajuan'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                $penggilingan = Penggilingan::create([
                    'tanggal_pengajuan'   => $pengData['tanggal_pengajuan'],
                    'nama_penggilingan'   => $entry['user']['nama_penggilingan'],
                    'lokasi_makloon'      => $pengData['lokasi_makloon'],
                    'status_verifikasi'   => $pengData['status_verifikasi'],
                    'catatan_verifikasi'  => $pengData['catatan_verifikasi'] ?? null,
                    'verified_at'         => in_array($pengData['status_verifikasi'], ['disetujui', 'ditolak'])
                        ? now()->subDays(rand(1, 10))
                        : null,
                ]);

                // Buat transport records
                foreach ($pengData['transports'] as $idx => $t) {
                    PenggilinganTransport::create([
                        'penggilingan_id' => $penggilingan->id,
                        'urutan'          => $idx + 1,
                        'nama_pengemudi'  => $t['nama_pengemudi'],
                        'nopol'           => $t['nopol'],
                        'kuantum'         => $t['kuantum'],
                    ]);
                }

                // Hitung ulang total tonase & jumlah angkutan
                $penggilingan->refresh();
                $penggilingan->calculateTotals();
            }
        }

        $this->command->info('✅ PenggilinganSeeder: 3 akun & 9 data penggilingan berhasil dibuat.');
    }
}
