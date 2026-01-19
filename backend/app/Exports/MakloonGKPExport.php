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

    public function __construct($year = null)
    {
        $this->year = $year ?? date('Y');
    }

    public function collection()
    {
        $penggilingan = Penggilingan::with(['transports' => function($query) {
            $query->orderBy('urutan');
        }])->get();

        $data = collect();
        $no = 1;

        foreach ($penggilingan as $peng) {
            if ($peng->transports->isEmpty()) {
                continue;
            }

            $totalKuantum = $peng->transports->sum('kuantum') * 1000; // Convert ton to kg
            $isFirstRow = true;

            foreach ($peng->transports as $transport) {
                $data->push([
                    $no++,
                    $isFirstRow ? $peng->lokasi_makloon : '',
                    $transport->nopol,
                    $transport->nama_pengemudi,
                    number_format($transport->kuantum * 1000, 0, ',', '.'), // Convert ton to kg
                    $isFirstRow ? number_format($totalKuantum, 0, ',', '.') : ''
                ]);
                $isFirstRow = false;
            }
        }

        $this->lastRow = $data->count() + 2; // +2 for header rows

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
        $lastRow = $this->lastRow;

        return [
            // Header row styling
            2 => [
                'font' => ['bold' => true, 'size' => 11],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7E6E6']],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ],
            // Data rows styling
            "3:{$lastRow}" => [
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ]
        ];
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
                
                // Center align for specific columns
                $lastRow = $this->lastRow + 1; // +1 because we added title row
                $sheet->getStyle("A3:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C3:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E3:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
