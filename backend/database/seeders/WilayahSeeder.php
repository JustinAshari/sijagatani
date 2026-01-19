<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // Contoh data DI Yogyakarta
        $yogyakarta = Provinsi::create(['nama' => 'DI Yogyakarta']);
        
        // Kabupaten
        $sleman = Kabupaten::create([
            'provinsi_id' => $yogyakarta->id,
            'nama' => 'Sleman'
        ]);
        
        $bantul = Kabupaten::create([
            'provinsi_id' => $yogyakarta->id,
            'nama' => 'Bantul'
        ]);
        
        // Kecamatan di Sleman
        $mlati = Kecamatan::create([
            'kabupaten_id' => $sleman->id,
            'nama' => 'Mlati'
        ]);
        
        $godean = Kecamatan::create([
            'kabupaten_id' => $sleman->id,
            'nama' => 'Godean'
        ]);
        
        // Kalurahan di Mlati
        Kalurahan::create([
            'kecamatan_id' => $mlati->id,
            'nama' => 'Tlogoadi'
        ]);
        
        Kalurahan::create([
            'kecamatan_id' => $mlati->id,
            'nama' => 'Sendangadi'
        ]);
        
        // Kalurahan di Godean
        Kalurahan::create([
            'kecamatan_id' => $godean->id,
            'nama' => 'Sidoarum'
        ]);
        
        // Contoh Provinsi lain
        $jawaTengah = Provinsi::create(['nama' => 'Jawa Tengah']);
        
        $klaten = Kabupaten::create([
            'provinsi_id' => $jawaTengah->id,
            'nama' => 'Klaten'
        ]);
        
        $kecDelanggu = Kecamatan::create([
            'kabupaten_id' => $klaten->id,
            'nama' => 'Delanggu'
        ]);
        
        Kalurahan::create([
            'kecamatan_id' => $kecDelanggu->id,
            'nama' => 'Karanganom'
        ]);
    }
}
