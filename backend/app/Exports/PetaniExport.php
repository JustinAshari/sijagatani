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
        $query = Petani::with('lahan');

        // Apply filters
        if (isset($this->filters['kabupaten'])) {
            $query->where('kabupaten', $this->filters['kabupaten']);
        }
        if (isset($this->filters['kecamatan'])) {
            $query->where('kecamatan', $this->filters['kecamatan']);
        }
        if (isset($this->filters['tanggal_dari'])) {
            $query->whereDate('created_at', '>=', $this->filters['tanggal_dari']);
        }
        if (isset($this->filters['tanggal_sampai'])) {
            $query->whereDate('created_at', '<=', $this->filters['tanggal_sampai']);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NIK',
            'Nama',
            'Alamat',
            'Kabupaten',
            'Kecamatan',
            'Desa',
            'No. Telepon',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Jumlah Lahan',
            'Tanggal Registrasi'
        ];
    }

    public function map($petani): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $petani->nik,
            $petani->nama,
            $petani->alamat,
            $petani->kabupaten,
            $petani->kecamatan,
            $petani->desa ?? '-',
            $petani->no_telepon ?? '-',
            $petani->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
            $petani->tanggal_lahir ? $petani->tanggal_lahir->format('d/m/Y') : '-',
            $petani->lahan->count(),
            $petani->created_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
