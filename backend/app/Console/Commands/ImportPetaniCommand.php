<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Petani;
use App\Models\Kalurahan;
use Illuminate\Support\Facades\DB;

class ImportPetaniCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:petani {file : Path to the CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data petani dari file CSV dengan validasi NIK dan mapping wilayah';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan di path: {$filePath}");
            return Command::FAILURE;
        }

        $file = fopen($filePath, 'r');
        
        // Membaca header baris pertama
        $header = fgetcsv($file, 1000, ';');
        // Jika pembatas ';' tidak mengembalikan lebih dari 1 kolom, coba pembatas ','
        if (count($header) <= 1) {
            rewind($file);
            $header = fgetcsv($file, 1000, ',');
            $delimiter = ',';
        } else {
            $delimiter = ';';
        }

        $this->info("Menggunakan pembatas CSV: '{$delimiter}'");

        $successCount = 0;
        $failedCount = 0;
        $skippedCount = 0;

        $this->info("Memulai proses integrasi data petani...");
        
        // Mulai transaksi database
        DB::beginTransaction();

        try {
            $rowNumber = 1;
            while (($row = fgetcsv($file, 1000, $delimiter)) !== FALSE) {
                $rowNumber++;
                
                // Skip baris kosong
                if (empty($row) || count($row) < 7) {
                    continue;
                }

                // Ambil data sesuai urutan kolom:
                // 0: NIK, 1: Nama, 2: Alamat, 3: Nama Kelurahan/Desa, 4: No Telepon, 5: Luas Lahan (Ha), 6: Komoditi (Gabah/Beras/Jagung), 7: Bank (Optional), 8: No Rekening (Optional)
                $nik = trim($row[0] ?? '');
                $nama = trim($row[1] ?? '');
                $alamat = trim($row[2] ?? '');
                $namaKalurahan = trim($row[3] ?? '');
                $noTelepon = trim($row[4] ?? '');
                $luasLahan = floatval(trim($row[5] ?? '0'));
                $komoditi = trim($row[6] ?? 'Gabah');
                $bank = trim($row[7] ?? '');
                $noRekening = trim($row[8] ?? '');

                // 1. Validasi NIK
                if (strlen($nik) !== 16 || !is_numeric($nik)) {
                    $this->error("Baris {$rowNumber}: NIK '{$nik}' tidak valid (harus 16 digit angka).");
                    $failedCount++;
                    continue;
                }

                // 2. Validasi NIK Duplikat
                if (Petani::where('nik', $nik)->exists()) {
                    $this->warn("Baris {$rowNumber}: NIK '{$nik}' sudah terdaftar (skip).");
                    $skippedCount++;
                    continue;
                }

                // 3. Validasi Komoditi
                if (!in_array($komoditi, ['Gabah', 'Jagung', 'Beras'])) {
                    $komoditi = 'Gabah'; // Fallback ke default
                }

                // 4. Mapping Wilayah berdasarkan Nama Kalurahan
                $kalurahan = Kalurahan::with('kecamatan.kabupaten.provinsi')
                    ->where('nama', 'LIKE', '%' . $namaKalurahan . '%')
                    ->first();

                if (!$kalurahan) {
                    $this->error("Baris {$rowNumber}: Kalurahan/Desa '{$namaKalurahan}' tidak ditemukan di database.");
                    $failedCount++;
                    continue;
                }

                // 5. Hitung Potensi Panen
                // Estimasi potensi panen standar (misal 5.5 ton atau 5500 kg per hektar)
                $potensiPanen = $luasLahan * 5500; 

                // 6. Simpan Data Petani
                Petani::create([
                    'tanggal' => now(),
                    'nik' => $nik,
                    'nama' => $nama,
                    'alamat' => $alamat,
                    'provinsi_id' => $kalurahan->kecamatan->kabupaten->provinsi_id ?? null,
                    'kabupaten_id' => $kalurahan->kecamatan->kabupaten_id ?? null,
                    'kecamatan_id' => $kalurahan->kecamatan_id ?? null,
                    'kalurahan_id' => $kalurahan->id,
                    'no_telepon' => $noTelepon,
                    'bank' => $bank ?: null,
                    'no_rekening' => $noRekening ?: null,
                    'luas_lahan' => $luasLahan,
                    'alamat_lahan' => $alamat, // default samakan dengan alamat rumah
                    'potensi_panen' => $potensiPanen,
                    'komoditi' => $komoditi,
                    'status_verifikasi' => 'pending', // Default pending
                ]);

                $successCount++;
            }

            DB::commit();
            fclose($file);

            $this->info("\n========================================");
            $this->info("    PROSES INTEGRASI SELESAI");
            $this->info("========================================");
            $this->info("Sukses diimpor : {$successCount} record");
            $this->warn("Duplikat (skip) : {$skippedCount} record");
            $this->error("Gagal (error)  : {$failedCount} record");
            $this->info("========================================");

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($file);
            $this->error("Terjadi kegagalan sistem: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
