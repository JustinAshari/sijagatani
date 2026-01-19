<?php

namespace App\Exports;

use App\Models\Kalurahan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WilayahExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Return collection with all data (based on Kalurahan with full hierarchy)
     */
    public function collection()
    {
        $kalurahan = Kalurahan::with([
            'kecamatan.kabupaten.provinsi'
        ])->get();
        
        $data = collect();
        $no = 1;
        
        foreach ($kalurahan as $kal) {
            $data->push([
                $no++,
                $kal->kecamatan->kabupaten->provinsi->nama ?? '',
                $kal->kecamatan->kabupaten->nama ?? '',
                $kal->kecamatan->nama ?? '',
                $kal->nama
            ]);
        }
        
        return $data;
    }

    /**
     * Return headers
     */
    public function headings(): array
    {
        return [
            'NO',
            'PROVINSI',
            'KABUPATEN',
            'KECAMATAN',
            'KELURAHAN/DESA'
        ];
    }

    /**
     * Styling
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    /**
     * Column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 25,
            'C' => 25,
            'D' => 25,
            'E' => 25,
        ];
    }
}
