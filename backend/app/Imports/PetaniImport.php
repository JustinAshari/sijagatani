<?php

namespace App\Imports;

use App\Models\Petani;
use App\Models\Kalurahan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PetaniImport implements ToModel, WithHeadingRow
{
    protected $successCount = 0;
    protected $failedCount = 0;
    protected $skippedCount = 0;
    protected $errors = [];

    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['nik']) || empty($row['nama'])) {
            return null;
        }

        $nik = trim((string) $row['nik']);
        $nama = trim($row['nama']);
        $alamat = trim($row['alamat'] ?? '-');
        
        // Find kelurahan key dynamically
        $kelurahanKey = null;
        foreach (['kelurahan', 'kelurahan_desa', 'kelurahandesa', 'kelurahan_atau_desa', 'desa'] as $key) {
            if (isset($row[$key])) {
                $kelurahanKey = $key;
                break;
            }
        }
        $namaKalurahan = $kelurahanKey ? trim($row[$kelurahanKey]) : '';
        
        $noTelepon = isset($row['no_telepon']) ? trim((string) $row['no_telepon']) : (isset($row['no_telp']) ? trim((string) $row['no_telp']) : '');
        $bank = trim($row['bank'] ?? '');
        $noRekening = isset($row['no_rekening']) ? trim((string) $row['no_rekening']) : (isset($row['no_rek']) ? trim((string) $row['no_rek']) : '');
        
        // Luas Lahan key lookup
        $luasLahanKey = null;
        foreach (['luas_lahan', 'luas_lahan_ha', 'luas', 'lahan'] as $key) {
            if (isset($row[$key])) {
                $luasLahanKey = $key;
                break;
            }
        }
        $luasLahan = $luasLahanKey ? floatval($row[$luasLahanKey]) : 0;
        
        $komoditi = trim($row['komoditi'] ?? 'Gabah');

        // Validation 1: NIK 16 digits
        if (strlen($nik) !== 16 || !is_numeric($nik)) {
            $this->failedCount++;
            $this->errors[] = "Baris NIK '{$nik}': Format NIK harus 16 digit angka.";
            return null;
        }

        // Validation 2: NIK unique
        if (Petani::where('nik', $nik)->exists()) {
            $this->skippedCount++;
            $this->errors[] = "Baris NIK '{$nik}': NIK sudah terdaftar (duplikat).";
            return null;
        }

        // Validate & adjust komoditi
        if (!in_array($komoditi, ['Gabah', 'Jagung', 'Beras'])) {
            $komoditi = 'Gabah';
        }

        try {
            DB::beginTransaction();

            // Lookup Kelurahan
            $kalurahan = Kalurahan::with('kecamatan.kabupaten.provinsi')
                ->where('nama', 'LIKE', '%' . $namaKalurahan . '%')
                ->first();

            if (!$kalurahan) {
                $this->failedCount++;
                $this->errors[] = "Baris Nama '{$nama}': Kelurahan/Desa '{$namaKalurahan}' tidak ditemukan.";
                DB::rollBack();
                return null;
            }

            $potensiPanen = $luasLahan * 5500; // default formula

            $petani = Petani::create([
                'tanggal' => now(),
                'nik' => $nik,
                'nama' => $nama,
                'alamat' => $alamat,
                'provinsi_id' => $kalurahan->kecamatan->kabupaten->provinsi_id ?? null,
                'kabupaten_id' => $kalurahan->kecamatan->kabupaten_id ?? null,
                'kecamatan_id' => $kalurahan->kecamatan_id ?? null,
                'kalurahan_id' => $kalurahan->id,
                'no_telepon' => $noTelepon ?: null,
                'bank' => $bank ?: null,
                'no_rekening' => $noRekening ?: null,
                'luas_lahan' => $luasLahan,
                'alamat_lahan' => $alamat,
                'potensi_panen' => $potensiPanen,
                'komoditi' => $komoditi,
                'status_verifikasi' => 'pending',
            ]);

            DB::commit();
            $this->successCount++;
            return $petani;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->failedCount++;
            $this->errors[] = "Baris Nama '{$nama}': Gagal menyimpan data - " . $e->getMessage();
            return null;
        }
    }

    public function getImportResults(): array
    {
        return [
            'success_count' => $this->successCount,
            'failed_count' => $this->failedCount,
            'skipped_count' => $this->skippedCount,
            'errors' => $this->errors
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }
}
