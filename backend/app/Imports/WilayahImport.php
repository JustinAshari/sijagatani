<?php

namespace App\Imports;

use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kalurahan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class WilayahImport implements ToModel, WithHeadingRow
{
    /**
     * Process each row from single sheet with full hierarchy
     */
    public function model(array $row)
    {
        // Check what key is actually used for kelurahan column
        $kelurahanKey = null;
        foreach (['kelurahan_desa', 'kelurahandesa', 'kelurahan/desa'] as $key) {
            if (isset($row[$key])) {
                $kelurahanKey = $key;
                break;
            }
        }
        
        // Skip empty rows
        if (empty($row['provinsi']) || empty($kelurahanKey) || empty($row[$kelurahanKey])) {
            return null;
        }

        try {
            DB::beginTransaction();
            
            // 1. Create/Get Provinsi
            $provinsi = Provinsi::firstOrCreate(
                ['nama' => trim($row['provinsi'])]
            );
            
            // 2. Create/Get Kabupaten
            $kabupaten = Kabupaten::firstOrCreate(
                [
                    'provinsi_id' => $provinsi->id,
                    'nama' => trim($row['kabupaten'])
                ]
            );
            
            // 3. Create/Get Kecamatan
            $kecamatan = Kecamatan::firstOrCreate(
                [
                    'kabupaten_id' => $kabupaten->id,
                    'nama' => trim($row['kecamatan'])
                ]
            );
            
            // 4. Create/Get Kalurahan
            $kalurahan = Kalurahan::firstOrCreate(
                [
                    'kecamatan_id' => $kecamatan->id,
                    'nama' => trim($row[$kelurahanKey])
                ]
            );
            
            DB::commit();
            return $kalurahan;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Error pada baris: " . $e->getMessage());
        }
    }

    /**
     * Heading row number
     */
    public function headingRow(): int
    {
        return 1;
    }
}
