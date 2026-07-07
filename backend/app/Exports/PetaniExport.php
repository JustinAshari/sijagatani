<?php

namespace App\Exports;

use App\Models\Petani;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PetaniExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Petani::with(['provinsi', 'kabupaten', 'kecamatan', 'kalurahan']);

        // Apply filters
        if (!empty($this->filters['provinsi_id'])) {
            $query->where('provinsi_id', $this->filters['provinsi_id']);
        }
        if (!empty($this->filters['kabupaten_id'])) {
            $query->where('kabupaten_id', $this->filters['kabupaten_id']);
        }
        if (!empty($this->filters['kecamatan_id'])) {
            $query->where('kecamatan_id', $this->filters['kecamatan_id']);
        }
        if (!empty($this->filters['kalurahan_id'])) {
            $query->where('kalurahan_id', $this->filters['kalurahan_id']);
        }
        if (!empty($this->filters['komoditi'])) {
            $query->where('komoditi', $this->filters['komoditi']);
        }
        if (!empty($this->filters['tanggal_dari'])) {
            $query->whereDate('tanggal', '>=', $this->filters['tanggal_dari']);
        }
        if (!empty($this->filters['tanggal_sampai'])) {
            $query->whereDate('tanggal', '<=', $this->filters['tanggal_sampai']);
        }

        return $query->latest('tanggal')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'NIK',
            'Nama',
            'No. Telepon',
            'Bank',
            'No. Rekening',
            'Provinsi',
            'Kabupaten',
            'Kecamatan',
            'Kalurahan',
            'Alamat',
            'Luas Lahan (Ha)',
            'Alamat Lahan',
            'Potensi Panen (Kg)',
            'Komoditi',
        ];
    }

    public function map($petani): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $petani->tanggal ? $petani->tanggal->format('d/m/Y') : '-',
            $petani->nik,
            $petani->nama,
            $petani->no_telepon ?? '-',
            $petani->bank ?? '-',
            $petani->no_rekening ?? '-',
            $petani->provinsi->nama ?? '-',
            $petani->kabupaten->nama ?? '-',
            $petani->kecamatan->nama ?? '-',
            $petani->kalurahan->nama ?? '-',
            $petani->alamat ?? '-',
            $petani->luas_lahan ?? 0,
            $petani->alamat_lahan ?? '-',
            $petani->potensi_panen ?? 0,
            $petani->komoditi ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
