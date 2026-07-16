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
        $query = Penggilingan::with(['provinsi', 'kabupaten', 'kecamatan', 'kalurahan', 'transports']);

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
        if (isset($this->filters['status_verifikasi'])) {
            $query->where('status_verifikasi', $this->filters['status_verifikasi']);
        }
        if (isset($this->filters['komoditas'])) {
            $query->where('komoditas', $this->filters['komoditas']);
        }
        if (isset($this->filters['provinsi_id'])) {
            $query->where('provinsi_id', $this->filters['provinsi_id']);
        }
        if (isset($this->filters['kabupaten_id'])) {
            $query->where('kabupaten_id', $this->filters['kabupaten_id']);
        }
        if (isset($this->filters['kecamatan_id'])) {
            $query->where('kecamatan_id', $this->filters['kecamatan_id']);
        }
        if (isset($this->filters['kalurahan_id'])) {
            $query->where('kalurahan_id', $this->filters['kalurahan_id']);
        }

        return $query->latest('tanggal_pengajuan')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pengajuan',
            'Nama Penggilingan',
            'Komoditas',
            'Provinsi',
            'Kabupaten',
            'Kecamatan',
            'Kalurahan',
            'Lokasi Makloon',
            'Total Tonase (Ton)',
            'Jumlah Angkutan',
            'Status Verifikasi',
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
                "Angkutan %d: %s (%s) - %.3f ton\n",
                $i + 1,
                $transport->nama_pengemudi,
                $transport->nopol,
                $transport->kuantum
            );
        }

        return [
            $index,
            $penggilingan->tanggal_pengajuan ? $penggilingan->tanggal_pengajuan->format('d-m-Y') : '-',
            $penggilingan->nama_penggilingan,
            $penggilingan->komoditas,
            $penggilingan->provinsi->nama ?? '-',
            $penggilingan->kabupaten->nama ?? '-',
            $penggilingan->kecamatan->nama ?? '-',
            $penggilingan->kalurahan->nama ?? '-',
            $penggilingan->lokasi_makloon,
            number_format($penggilingan->total_tonase, 3, ',', '.'),
            $penggilingan->jumlah_angkutan,
            ucfirst($penggilingan->status_verifikasi ?? 'pending'),
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
