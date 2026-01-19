<?php

namespace App\Http\Controllers;

use App\Models\Provinsi;
use App\Exports\WilayahExport;
use App\Imports\WilayahImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class WilayahController extends Controller
{
    // COMBINED EXPORT/IMPORT (All Wilayah in one file)
    public function exportWilayah()
    {
        return Excel::download(new WilayahExport, 'data_wilayah_' . time() . '.xlsx');
    }

    public function importWilayah(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv|max:5120'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            Excel::import(new WilayahImport, $request->file('file'));
            
            return response()->json([
                'success' => true,
                'message' => 'Data wilayah (Provinsi, Kabupaten, Kecamatan, Kalurahan) berhasil diimport'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal import data: ' . $e->getMessage()
            ], 422);
        }
    }

    public function templateWilayah()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Wilayah');
        
        // HEADER
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'PROVINSI');
        $sheet->setCellValue('C1', 'KABUPATEN');
        $sheet->setCellValue('D1', 'KECAMATAN');
        $sheet->setCellValue('E1', 'KELURAHAN/DESA');
        
        // Style header
        $sheet->getStyle('A1:E1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:E1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        
        // Example data
        $sheet->setCellValue('A2', '1');
        $sheet->setCellValue('B2', 'Jawa Tengah');
        $sheet->setCellValue('C2', 'Klaten');
        $sheet->setCellValue('D2', 'Juwiring');
        $sheet->setCellValue('E2', 'Trasan');
        
        $sheet->setCellValue('A3', '2');
        $sheet->setCellValue('B3', 'Jawa Tengah');
        $sheet->setCellValue('C3', 'Klaten');
        $sheet->setCellValue('D3', 'Juwiring');
        $sheet->setCellValue('E3', 'Sawahan');
        
        $sheet->setCellValue('A4', '3');
        $sheet->setCellValue('B4', 'Jawa Tengah');
        $sheet->setCellValue('C4', 'Klaten');
        $sheet->setCellValue('D4', 'Juwiring');
        $sheet->setCellValue('E4', 'Juwiran');
        
        $sheet->setCellValue('A5', '4');
        $sheet->setCellValue('B5', 'Jawa Tengah');
        $sheet->setCellValue('C5', 'Klaten');
        $sheet->setCellValue('D5', 'Juwiring');
        $sheet->setCellValue('E5', 'Jetis');
        
        // Column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_data_wilayah.xlsx';
        
        // Create temp file
        $temp_file = tempnam(sys_get_temp_dir(), 'template');
        $writer->save($temp_file);
        
        return response()->download($temp_file, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
