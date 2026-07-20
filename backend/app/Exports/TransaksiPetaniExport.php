<?php

namespace App\Exports;

use App\Models\TransaksiPetani;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransaksiPetaniExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = TransaksiPetani::with(['petani', 'verifier']);

        // Apply filters
        if (!empty($this->filters['tanggal_dari'])) {
            $query->whereDate('tanggal_transaksi', '>=', $this->filters['tanggal_dari']);
        }
        if (!empty($this->filters['tanggal_sampai'])) {
            $query->whereDate('tanggal_transaksi', '<=', $this->filters['tanggal_sampai']);
        }
        if (!empty($this->filters['status_transaksi'])) {
            $query->where('status_transaksi', $this->filters['status_transaksi']);
        }
        if (!empty($this->filters['status_verifikasi'])) {
            $query->where('status_verifikasi', $this->filters['status_verifikasi']);
        }
        if (!empty($this->filters['komoditas'])) {
            $query->where('komoditas', $this->filters['komoditas']);
        }

        if (!empty($this->filters['provinsi_id'])) {
            $query->whereHas('petani', function ($q) {
                $q->where('provinsi_id', $this->filters['provinsi_id']);
            });
        }
        if (!empty($this->filters['kabupaten_id'])) {
            $query->whereHas('petani', function ($q) {
                $q->where('kabupaten_id', $this->filters['kabupaten_id']);
            });
        }
        if (!empty($this->filters['kecamatan_id'])) {
            $query->whereHas('petani', function ($q) {
                $q->where('kecamatan_id', $this->filters['kecamatan_id']);
            });
        }
        if (!empty($this->filters['kalurahan_id'])) {
            $query->whereHas('petani', function ($q) {
                $q->where('kalurahan_id', $this->filters['kalurahan_id']);
            });
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->whereHas('petani', function ($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        return $query->latest('tanggal_transaksi')->latest('id')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Transaksi',
            'Nama Petani',
            'NIK Petani',
            'Komoditas',
            'Volume (KG)',
            'Harga/KG (Rp)',
            'Nominal (Rp)',
            'Bank',
            'No. Rekening',
            'Status Transfer',
            'Status Verifikasi',
            'Catatan Verifikasi',
            'Diverifikasi Oleh',
            'Waktu Verifikasi',
        ];
    }

    public function map($tx): array
    {
        static $no = 0;
        $no++;

        $statusTransfer = $tx->status_transaksi === 'sudah' ? 'Sudah Transfer' : ($tx->status_transaksi === 'ditolak' ? 'Ditolak' : 'Belum Transfer');
        $statusVerifikasi = $tx->status_verifikasi === 'disetujui' ? 'Disetujui' : ($tx->status_verifikasi === 'ditolak' ? 'Ditolak' : 'Pending');

        return [
            $no,
            $tx->tanggal_transaksi ? $tx->tanggal_transaksi->format('d/m/Y') : '-',
            $tx->petani->nama ?? '-',
            $tx->petani->nik ?? '-',
            $tx->komoditas ?? '-',
            $tx->volume_kg ?? 0,
            $tx->harga_per_kg ?? 0,
            $tx->nominal ?? 0,
            $tx->bank ?? '-',
            $tx->no_rekening ?? '-',
            $statusTransfer,
            $statusVerifikasi,
            $tx->catatan_verifikasi ?? '-',
            $tx->verifier->name ?? '-',
            $tx->verified_at ? $tx->verified_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
