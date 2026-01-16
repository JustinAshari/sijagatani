<?php

namespace App\Exports;

use App\Models\Penggilingan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Request;

class PenggilinganExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Penggilingan::with(['petani', 'transports']);

        // Apply filters
        if (isset($this->filters['tanggal_dari'])) {
            $query->whereDate('tanggal_pengajuan', '>=', $this->filters['tanggal_dari']);
        }
        if (isset($this->filters['tanggal_sampai'])) {
            $query->whereDate('tanggal_pengajuan', '<=', $this->filters['tanggal_sampai']);
        }
        if (isset($this->filters['nama_penggilingan'])) {
            $query->where('nama_penggilingan', 'LIKE', '%' . $this->filters['nama_penggilingan'] . '%');
        }
        if (isset($this->filters['kabupaten'])) {
            $query->whereHas('petani', function($q) {
                $q->where('kabupaten', $this->filters['kabupaten']);
            });
        }

        return $query->latest('tanggal_pengajuan')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pengajuan',
            'Nama Penggilingan',
            'NIK Petani',
            'Nama Petani',
            'Kabupaten',
            'Kecamatan',
            'Lokasi Makloon',
            'Total Tonase (Ton)',
            'Jumlah Angkutan',
            'Detail Transportasi'
        ];
    }

    public function map($penggilingan): array
    {
        static $index = 0;
        $index++;

        // Build transport details string
        $transportDetails = '';
        foreach ($penggilingan->transports as $i => $transport) {
            $transportDetails .= sprintf(
                "Transport %d: %s (%s) - %.3f ton\n",
                $i + 1,
                $transport->nama_pengemudi,
                $transport->nopol,
                $transport->kuantum
            );
        }

        return [
            $index,
            $penggilingan->tanggal_pengajuan->format('d-m-Y'),
            $penggilingan->nama_penggilingan,
            $penggilingan->petani->nik ?? '-',
            $penggilingan->petani->nama ?? '-',
            $penggilingan->petani->kabupaten ?? '-',
            $penggilingan->petani->kecamatan ?? '-',
            $penggilingan->lokasi_makloon,
            number_format($penggilingan->total_tonase, 3, ',', '.'),
            $penggilingan->jumlah_angkutan,
            trim($transportDetails)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
