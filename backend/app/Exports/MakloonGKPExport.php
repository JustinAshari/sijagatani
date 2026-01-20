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
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MakloonGKPExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $year;
    protected $lastRow;
    protected $penggilinganId;
    protected $penggilinganData;

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
        
        // Store penggilingan data for use in registerEvents
        $this->penggilinganData = $penggilingan;

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
            'G' => 3,   // Gap column
            'H' => 35,  // Lampiran (merged with I)
            'I' => 35,  // Lampiran (merged with H)
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
                
                // Add Lampiran section (beside the table, starting from column H)
                // Calculate heights for each lampiran box
                $boxHeight = 15; // rows per lampiran box
                
                // Lampiran 3 (Nota Timbang) - starts 1 row below header
                $lampiran3Start = 3;
                $lampiran3End = $lampiran3Start + $boxHeight - 1;
                $sheet->setCellValue("H{$lampiran3Start}", 'CONTOH LAMPIRAN 3 (NOTA TIMBANG)');
                $sheet->mergeCells("H{$lampiran3Start}:I{$lampiran3Start}");
                $sheet->getStyle("H{$lampiran3Start}:I{$lampiran3Start}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E7E6E6']
                    ],
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
                $lampiran3BoxStart = $lampiran3Start + 1;
                $sheet->mergeCells("H{$lampiran3BoxStart}:I{$lampiran3End}");
                $sheet->getStyle("H{$lampiran3BoxStart}:I{$lampiran3End}")->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
                
                // Lampiran 4 (Surat Jalan) - 1 row gap after Lampiran 3
                $lampiran4Start = $lampiran3End + 2;
                $lampiran4End = $lampiran4Start + $boxHeight;
                $sheet->setCellValue("H{$lampiran4Start}", 'CONTOH LAMPIRAN 4 (SURAT JALAN)');
                $sheet->mergeCells("H{$lampiran4Start}:I{$lampiran4Start}");
                $sheet->getStyle("H{$lampiran4Start}:I{$lampiran4Start}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E7E6E6']
                    ],
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
                $lampiran4BoxStart = $lampiran4Start + 1;
                $sheet->mergeCells("H{$lampiran4BoxStart}:I{$lampiran4End}");
                $sheet->getStyle("H{$lampiran4BoxStart}:I{$lampiran4End}")->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
                
                // Lampiran 5 (Foto Kegiatan) - 1 row gap after Lampiran 4
                $lampiran5Start = $lampiran4End + 2;
                $lampiran5End = $lampiran5Start + $boxHeight;
                $sheet->setCellValue("H{$lampiran5Start}", 'CONTOH LAMPIRAN 5 (FOTO KEGIATAN)');
                $sheet->mergeCells("H{$lampiran5Start}:I{$lampiran5Start}");
                $sheet->getStyle("H{$lampiran5Start}:I{$lampiran5Start}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E7E6E6']
                    ],
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
                $lampiran5BoxStart = $lampiran5Start + 1;
                $sheet->mergeCells("H{$lampiran5BoxStart}:I{$lampiran5End}");
                $sheet->getStyle("H{$lampiran5BoxStart}:I{$lampiran5End}")->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
                
                // Insert photos if available
                $this->insertPhotos($sheet, $lampiran3BoxStart, $lampiran4BoxStart, $lampiran5BoxStart);
            },
        ];
    }
    
    /**
     * Insert photos into lampiran boxes
     */
    private function insertPhotos($sheet, $lampiran3Row, $lampiran4Row, $lampiran5Row)
    {
        $penggilingan = $this->penggilinganData;
        $storagePath = storage_path('app/public/');
        
        // Lampiran 3: Nota Timbang (from ALL transports)
        $notaOffsetY = 5;
        foreach ($penggilingan->transports as $index => $transport) {
            if ($transport->nota_timbang) {
                $notaPath = $storagePath . $transport->nota_timbang;
                if (file_exists($notaPath) && $this->isImageFile($notaPath)) {
                    $this->addImageToCell($sheet, $notaPath, "H{$lampiran3Row}", 500, 200, 5, $notaOffsetY);
                    $notaOffsetY += 210; // Stack vertically with 10px gap
                }
            }
        }
        
        // Lampiran 4: Surat Jalan (from ALL transports)
        $suratOffsetY = 5;
        foreach ($penggilingan->transports as $index => $transport) {
            if ($transport->surat_jalan) {
                $suratPath = $storagePath . $transport->surat_jalan;
                if (file_exists($suratPath) && $this->isImageFile($suratPath)) {
                    $this->addImageToCell($sheet, $suratPath, "H{$lampiran4Row}", 500, 200, 5, $suratOffsetY);
                    $suratOffsetY += 210; // Stack vertically with 10px gap
                }
            }
        }
        
        // Lampiran 5: Foto Kegiatan GKP (foto_gkp_1 and foto_gkp_2)
        $fotoOffsetY = 5;
        
        if ($penggilingan->foto_gkp_1) {
            $foto1Path = $storagePath . $penggilingan->foto_gkp_1;
            if (file_exists($foto1Path) && $this->isImageFile($foto1Path)) {
                $this->addImageToCell($sheet, $foto1Path, "H{$lampiran5Row}", 500, 300, 5, $fotoOffsetY);
                $fotoOffsetY += 310;
            }
        }
        
        if ($penggilingan->foto_gkp_2) {
            $foto2Path = $storagePath . $penggilingan->foto_gkp_2;
            if (file_exists($foto2Path) && $this->isImageFile($foto2Path)) {
                $this->addImageToCell($sheet, $foto2Path, "H{$lampiran5Row}", 500, 300, 5, $fotoOffsetY);
            }
        }
    }
    
    /**
     * Check if file is an image
     */
    private function isImageFile($filePath)
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp']);
    }
    
    /**
     * Add image to specific cell
     */
    private function addImageToCell($sheet, $imagePath, $coordinate, $width = 300, $height = 300, $offsetX = 5, $offsetY = 5)
    {
        $drawing = new Drawing();
        $drawing->setName('Image');
        $drawing->setDescription('Image');
        $drawing->setPath($imagePath);
        $drawing->setCoordinates($coordinate);
        $drawing->setWidth($width);
        $drawing->setHeight($height);
        $drawing->setOffsetX($offsetX);
        $drawing->setOffsetY($offsetY);
        $drawing->setWorksheet($sheet);
    }
}
