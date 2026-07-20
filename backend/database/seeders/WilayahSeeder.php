<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kalurahan;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Provinsi Jawa Tengah
        $provinsi = Provinsi::firstOrCreate(
            ['id' => 2],
            ['nama' => 'Jawa Tengah']
        );

        // 2. Daftar Kabupaten dengan ID yang sesuai dengan PetaniSeeder
        $kabupatens = [
            7  => 'Kab. Boyolali',
            9  => 'Kab. Karanganyar',
            12 => 'Kab. Klaten',
            11 => 'Kab. Sragen',
            8  => 'Kab. Sukoharjo',
            10 => 'Kab. Wonogiri',
            6  => 'Kota Surakarta'
        ];

        // 3. Struktur Kecamatan dan Kalurahan/Desa untuk masing-masing Kabupaten
        $dataWilayah = [
            'Kab. Boyolali' => [
                'Boyolali' => ['Banaran', 'Boyolali', 'Karanggeneng', 'Mulyorejo', 'Pulisen'],
                'Ampel' => ['Ampel', 'Banyuanyar', 'Candi', 'Gladagsari', 'Selodoko'],
                'Cepogo' => ['Bakulan', 'Cabean Kunti', 'Cepogo', 'Menden', 'Paras'],
                'Simo' => ['Simo', 'Blagung', 'Kedunglengkong', 'Pentur', 'Walen'],
                'Banyudono' => ['Banyudono', 'Baturagung', 'Candi', 'Ketaon', 'Ngaru-Aru']
            ],
            'Kab. Karanganyar' => [
                'Karanganyar' => ['Bejen', 'Cangakan', 'Karanganyar', 'Lalung', 'Tegalgede'],
                'Colomadu' => ['Baturan', 'Blulukan', 'Gajahan', 'Klodran', 'Tohudan'],
                'Tawangmangu' => ['Gondosuli', 'Kalisoro', 'Karanglo', 'Nglurah', 'Tawangmangu'],
                'Jaten' => ['Jaten', 'Dagen', 'Ngringo', 'Sroyo', 'Jetis'],
                'Kebakkramat' => ['Kebak', 'Alastuwo', 'Banjarharjo', 'Kemiri', 'Warusekumpul']
            ],
            'Kab. Klaten' => [
                'Klaten Tengah' => ['Buntalan', 'Kabupaten', 'Klaten', 'Mojayan', 'Tonggalan'],
                'Delanggu' => ['Delanggu', 'Gombang', 'Karanganglo', 'Sabrang', 'Tegalrejo'],
                'Prambanan' => ['Bugisan', 'Geneng', 'Kemudo', 'Kokosan', 'Randusari'],
                'Ceper' => ['Ceper', 'Cawan', 'Jambu Kulon', 'Kurung', 'Kujon'],
                'Pedan' => ['Pedan', 'Bendo', 'Kedungupi', 'Keden', 'Temon']
            ],
            'Kab. Sragen' => [
                'Sragen' => ['Karang Tengah', 'Nglorog', 'Sine', 'Sragen Kulon', 'Sragen Wetan'],
                'Masaran' => ['Jati', 'Karangmalang', 'Krikilan', 'Masaran', 'Pringanom'],
                'Gemolong' => ['Gemolong', 'Genengduwur', 'Kaluran', 'Nganti', 'Purworejo'],
                'Sidoharjo' => ['Sidoharjo', 'Jetak', 'Patihan', 'Purwosuman', 'Singo']
            ],
            'Kab. Sukoharjo' => [
                'Sukoharjo' => ['Begajah', 'Gayam', 'Jetis', 'Joho', 'Sukoharjo'],
                'Grogol' => ['Banaran', 'Cemani', 'Grogol', 'Madegondo', 'Sanggrahan'],
                'Kartasura' => ['Kartasura', 'Singopuran', 'Pabelan', 'Makamhaji', 'Gonilan'],
                'Baki' => ['Baki Pandeyan', 'Gentingan', 'Kudu', 'Mancasan', 'Siwal'],
                'Mojolaban' => ['Bekonang', 'Cangkol', 'Duku', 'Gadingan', 'Labasan']
            ],
            'Kab. Wonogiri' => [
                'Wonogiri' => ['Giritontro', 'Giriwono', 'Pokoh Kidul', 'Wuryorejo', 'Wonoboyo'],
                'Baturetno' => ['Balepanjang', 'Baturetno', 'Glesungrejo', 'Temon', 'Watuagung'],
                'Pracimantoro' => ['Banaran', 'Glinggang', 'Joho', 'Pracimantoro', 'Sambiroto'],
                'Eromoko' => ['Eromoko', 'Baleharjo', 'Minggarharjo', 'Pucung', 'Sindukarto']
            ],
            'Kota Surakarta' => [
                'Laweyan' => ['Laweyan', 'Pajang', 'Penumping', 'Purwosari', 'Sondakan'],
                'Banjarsari' => ['Banjarsari', 'Gilingan', 'Kadipiro', 'Nusukan', 'Keprabon'],
                'Jebres' => ['Jebres', 'Mojosongo', 'Pucangsawit', 'Purwodiningratan', 'Sewu'],
                'Pasar Kliwon' => ['Pasar Kliwon', 'Mangkubumen', 'Kauman', 'Baluwarti', 'Semanggi'],
                'Serengan' => ['Serengan', 'Joyotakan', 'Kratonan', 'Tipes', 'Kemlayan']
            ]
        ];

        foreach ($kabupatens as $kabId => $kabNama) {
            $kabupaten = Kabupaten::firstOrCreate(
                ['id' => $kabId],
                ['provinsi_id' => $provinsi->id, 'nama' => $kabNama]
            );

            if (isset($dataWilayah[$kabNama])) {
                foreach ($dataWilayah[$kabNama] as $kecNama => $kalurahans) {
                    $kecamatan = Kecamatan::firstOrCreate(
                        ['kabupaten_id' => $kabupaten->id, 'nama' => $kecNama]
                    );

                    foreach ($kalurahans as $kalNama) {
                        Kalurahan::firstOrCreate(
                            ['kecamatan_id' => $kecamatan->id, 'nama' => $kalNama]
                        );
                    }
                }
            }
        }

        $this->command->info('✅ WilayahSeeder: Data Provinsi, Kabupaten, Kecamatan, dan Kalurahan berhasil ditambahkan.');
    }
}
