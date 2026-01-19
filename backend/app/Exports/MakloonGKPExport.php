<?php

namespace App\Exports;

use App\Models\Penggilingan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MakloonGKPExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $year;
    protected $lastRow;
    protected $penggilinganId;

    public function __construct($penggilinganId, $year = null)
    {
        $this->penggilinganId = $penggilinganId;
        $this->year = $year ?? date('Y');
    }

    public function collection()
    {
        $penggilingan = Penggilingan::with(['transports' => function($query) {
            $query->orderBy('urutan');
        }])->findOrFail($this->penggilinganId);

        $data = collect();
        $no = 1;

        if ($penggilingan->transports->isEmpty()) {
            return $data;
        }

        $totalKuantum = $penggilingan->transports->sum('kuantum') * 1000; // Convert ton to kg
        $totalKuantumFormatted = number_format($totalKuantum, 0, ',', '.');
        $isFirstRow = true;

        foreach ($penggilingan->transports as $transport) {
            $data->push([
                $no++,
                $penggilingan->lokasi_makloon,
                $transport->nopol,
                $transport->nama_pengemudi,
                number_format($transport->kuantum * 1000, 0, ',', '.'), // Convert ton to kg
                $isFirstRow ? $totalKuantumFormatted : ''
            ]);
            $isFirstRow = false;
        }

        // lastRow is data count only (will add title+header offset in registerEvents)
        $this->lastRow = $data->count();

        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tempat Maklon GKP',
            'Nopol Angkutan',
            'Pengemudi',
            'Kuantum (Kg)',
            'Total Kuantum GKP (Kg)'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // No styling here, will be done in registerEvents
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // No
            'B' => 35,  // Tempat Maklon GKP
            'C' => 18,  // Nopol Angkutan
            'D' => 20,  // Pengemudi
            'E' => 18,  // Kuantum (Kg)
            'F' => 20,  // Total Kuantum GKP (Kg)
        ];
    }

    public function title(): string
    {
        return 'Data Makloon GKP ' . $this->year;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Insert title row at the top
                $sheet->insertNewRowBefore(1, 1);
                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', 'FORM PENGAJUAN GKP MAKLON MPP TAHUN ' . $this->year);
                
                // Style title
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);
                
                $sheet->getRowDimension(1)->setRowHeight(25);
                
                // Style header row (row 2 after title insert)
                $sheet->getStyle('A2:F2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E7E6E6']
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
                
                // Apply border and alignment to data rows (only columns A-F)
                // lastRow = data count, +2 for title and header rows
                $lastDataRow = $this->lastRow + 2;
                $sheet->getStyle("A3:F{$lastDataRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);
                
                // Center align for specific columns in data rows
                $sheet->getStyle("A3:A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C3:C{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E3:F{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Add Catatan section (2 rows below data table)
                $catatanStartRow = $lastDataRow + 2;
                $sheet->setCellValue("A{$catatanStartRow}", 'Catatan');
                $sheet->getStyle("A{$catatanStartRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11]
                ]);
                
                // Add catatan items
                $catatan = [
                    '1' => 'Pengajuan Makloon sesuai dengan kepemilikan infrastruktur  (Dryer - RMU)',
                    '2' => 'Jaminan makloon dan TTD perjanjian dibayarkan sebelum pengajuan PO',
                    '3' => 'Nota Timbang berdasarkan kuantum dikirimkan asli ke Kantor Cabang atau kirim PDF',
                    '4' => 'Surat Jalan per angkutan dikirimkan asli ke Kantor Cabang atau kirim PDF',
                    '5' => 'Foto kegiatan GKP di sawah dan Penggilingan dikirimkan asli ke Kantor Cabang atau kirim PDF'
                ];
                
                $catatanRow = $catatanStartRow + 1;
                foreach ($catatan as $no => $text) {
                    $sheet->setCellValue("A{$catatanRow}", $no);
                    $sheet->setCellValue("B{$catatanRow}", $text);
                    $sheet->mergeCells("B{$catatanRow}:F{$catatanRow}");
                    $catatanRow++;
                }
            },
        ];
    }
}
