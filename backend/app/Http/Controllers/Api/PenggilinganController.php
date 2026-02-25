<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penggilingan;
use App\Models\PenggilinganTransport;
use App\Models\Petani;
use App\Services\ImageService;
use App\Exports\PenggilinganExport;
use App\Exports\MakloonGKPExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class PenggilinganController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of penggilingan with filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Penggilingan::with(['transports']);

        // Filter by nama penggilingan
        if ($request->has('nama_penggilingan')) {
            $query->where('nama_penggilingan', 'LIKE', '%' . $request->nama_penggilingan . '%');
        }

        // Filter by tanggal
        if ($request->has('tanggal_dari')) {
            $query->whereDate('tanggal_pengajuan', '>=', $request->tanggal_dari);
        }
        if ($request->has('tanggal_sampai')) {
            $query->whereDate('tanggal_pengajuan', '<=', $request->tanggal_sampai);
        }

        // Removed kabupaten filter since we no longer have petani relationship

        $penggilingan = $query->latest('tanggal_pengajuan')->get();

        return response()->json([
            'success' => true,
            'data' => $penggilingan
        ]);
    }

    /**
     * Store a newly created penggilingan
     */
    public function store(Request $request): JsonResponse
    {
        // Filter out empty file fields before validation
        $data = $request->all();
        if (!$request->hasFile('foto_gkp_1')) {
            unset($data['foto_gkp_1']);
        }
        if (!$request->hasFile('foto_gkp_2')) {
            unset($data['foto_gkp_2']);
        }


        $validator = Validator::make($data, [
            'tanggal_pengajuan' => 'required|date',
            'petani_id' => 'required|string|max:255',  // Changed to string (nama petani)
            'nama_penggilingan' => 'required|string|max:255',
            'lokasi_makloon' => 'required|string|max:255',
            'foto_gkp_1' => 'sometimes|file|mimes:jpeg,png,jpg|max:5120',
            'foto_gkp_2' => 'sometimes|file|mimes:jpeg,png,jpg|max:5120',
            
            // Transport data (dynamic 1-20)
            'transports' => 'required|array|min:1|max:20',
            'transports.*.nama_pengemudi' => 'required|string|max:255',
            'transports.*.nopol' => 'required|string|max:20',
            'transports.*.kuantum' => 'required|numeric|min:0',
            // nota_timbang is handled separately during file upload
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = [
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'nama_penggilingan' => $request->nama_penggilingan,
                'lokasi_makloon' => $request->lokasi_makloon
            ];

            // Handle image uploads for GKP
            if ($request->hasFile('foto_gkp_1')) {
                $data['foto_gkp_1'] = $this->imageService->uploadAndCompress(
                    $request->file('foto_gkp_1'),
                    'penggilingan/gkp',
                    800,
                    70
                );
            }

            if ($request->hasFile('foto_gkp_2')) {
                $data['foto_gkp_2'] = $this->imageService->uploadAndCompress(
                    $request->file('foto_gkp_2'),
                    'penggilingan/gkp',
                    800,
                    70
                );
            }

            // Create penggilingan record
            $penggilingan = Penggilingan::create($data);

            // Create transport records
            $transports = $request->input('transports', []);
            foreach ($transports as $index => $transportData) {
                $transportRecord = [
                    'penggilingan_id' => $penggilingan->id,
                    'urutan' => $index + 1,
                    'nama_pengemudi' => $transportData['nama_pengemudi'],
                    'nopol' => $transportData['nopol'],
                    'kuantum' => $transportData['kuantum'],
                ];

                // Handle nota timbang upload - check from request directly
                $notaTimbangKey = "transports.{$index}.nota_timbang";
                if ($request->hasFile($notaTimbangKey)) {
                    $transportRecord['nota_timbang'] = $this->imageService->uploadAndCompress(
                        $request->file($notaTimbangKey),
                        'penggilingan/nota',
                        800,
                        70
                    );
                }

                // Handle surat jalan upload - check from request directly
                $suratJalanKey = "transports.{$index}.surat_jalan";
                if ($request->hasFile($suratJalanKey)) {
                    $transportRecord['surat_jalan'] = $this->imageService->uploadAndCompress(
                        $request->file($suratJalanKey),
                        'penggilingan/surat',
                        800,
                        70
                    );
                }

                PenggilinganTransport::create($transportRecord);
            }

            // Calculate totals
            $penggilingan->refresh();
            $penggilingan->calculateTotals();
            $penggilingan->load(['transports']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data penggilingan berhasil ditambahkan',
                'data' => $penggilingan
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data penggilingan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified penggilingan
     */
    public function show(string $id): JsonResponse
    {
        $penggilingan = Penggilingan::with(['petani', 'transports'])->find($id);

        if (!$penggilingan) {
            return response()->json([
                'success' => false,
                'message' => 'Data penggilingan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $penggilingan
        ]);
    }

    /**
     * Update the specified penggilingan
     */
    public function update(Request $request, string $id): JsonResponse
    {
        // Filter out empty file fields before validation
        $data = $request->all();
        if (!$request->hasFile('foto_gkp_1')) {
            unset($data['foto_gkp_1']);
        }
        if (!$request->hasFile('foto_gkp_2')) {
            unset($data['foto_gkp_2']);
        }

        $validator = Validator::make($data, [
            'tanggal_pengajuan' => 'required|date',
            'nama_penggilingan' => 'required|string|max:255',
            'lokasi_makloon' => 'required|string|max:255',
            'foto_gkp_1' => 'sometimes|file|mimes:jpeg,png,jpg|max:5120',
            'foto_gkp_2' => 'sometimes|file|mimes:jpeg,png,jpg|max:5120',
            
            'transports' => 'required|array|min:1|max:20',
            'transports.*.nama_pengemudi' => 'required|string|max:255',
            'transports.*.nopol' => 'required|string|max:20',
            'transports.*.kuantum' => 'required|numeric|min:0',
            // nota_timbang is handled separately during file upload
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $penggilingan = Penggilingan::find($id);

            if (!$penggilingan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penggilingan tidak ditemukan'
                ], 404);
            }

            $data = [
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'nama_penggilingan' => $request->nama_penggilingan,
                'lokasi_makloon' => $request->lokasi_makloon
            ];

            // Handle image uploads
            if ($request->hasFile('foto_gkp_1')) {
                if ($penggilingan->foto_gkp_1) {
                    $this->imageService->delete($penggilingan->foto_gkp_1);
                }
                $data['foto_gkp_1'] = $this->imageService->uploadAndCompress(
                    $request->file('foto_gkp_1'),
                    'penggilingan/gkp',
                    800,
                    70
                );
            } elseif ($request->has('old_foto_gkp_1')) {
                $data['foto_gkp_1'] = $request->input('old_foto_gkp_1');
            }

            if ($request->hasFile('foto_gkp_2')) {
                if ($penggilingan->foto_gkp_2) {
                    $this->imageService->delete($penggilingan->foto_gkp_2);
                }
                $data['foto_gkp_2'] = $this->imageService->uploadAndCompress(
                    $request->file('foto_gkp_2'),
                    'penggilingan/gkp',
                    800,
                    70
                );
            } elseif ($request->has('old_foto_gkp_2')) {
                $data['foto_gkp_2'] = $request->input('old_foto_gkp_2');
            }

            $penggilingan->update($data);

            // Delete old transports and create new ones
            foreach ($penggilingan->transports as $transport) {
                if ($transport->nota_timbang) {
                    $this->imageService->delete($transport->nota_timbang);
                }
                if ($transport->surat_jalan) {
                    $this->imageService->delete($transport->surat_jalan);
                }
            }
            $penggilingan->transports()->delete();

            // Create new transport records
            $transports = $request->input('transports', []);
            foreach ($transports as $index => $transportData) {
                $transportRecord = [
                    'penggilingan_id' => $penggilingan->id,
                    'urutan' => $index + 1,
                    'nama_pengemudi' => $transportData['nama_pengemudi'],
                    'nopol' => $transportData['nopol'],
                    'kuantum' => $transportData['kuantum'],
                ];

                // Handle nota timbang upload - check from request directly
                $notaTimbangKey = "transports.{$index}.nota_timbang";
                if ($request->hasFile($notaTimbangKey)) {
                    $transportRecord['nota_timbang'] = $this->imageService->uploadAndCompress(
                        $request->file($notaTimbangKey),
                        'penggilingan/nota',
                        800,
                        70
                    );
                } elseif (isset($transportData['old_nota_timbang']) && !empty($transportData['old_nota_timbang'])) {
                    $transportRecord['nota_timbang'] = $transportData['old_nota_timbang'];
                }

                // Handle surat jalan upload - check from request directly
                $suratJalanKey = "transports.{$index}.surat_jalan";
                if ($request->hasFile($suratJalanKey)) {
                    $transportRecord['surat_jalan'] = $this->imageService->uploadAndCompress(
                        $request->file($suratJalanKey),
                        'penggilingan/surat',
                        800,
                        70
                    );
                } elseif (isset($transportData['old_surat_jalan']) && !empty($transportData['old_surat_jalan'])) {
                    $transportRecord['surat_jalan'] = $transportData['old_surat_jalan'];
                }

                PenggilinganTransport::create($transportRecord);
            }

            // Recalculate totals
            $penggilingan->refresh();
            $penggilingan->calculateTotals();
            $penggilingan->load(['transports']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data penggilingan berhasil diupdate',
                'data' => $penggilingan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data penggilingan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified penggilingan
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $penggilingan = Penggilingan::with('transports')->find($id);

            if (!$penggilingan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penggilingan tidak ditemukan'
                ], 404);
            }

            // Delete all images
            if ($penggilingan->foto_gkp_1) {
                $this->imageService->delete($penggilingan->foto_gkp_1);
            }
            if ($penggilingan->foto_gkp_2) {
                $this->imageService->delete($penggilingan->foto_gkp_2);
            }

            // Delete transport images
            foreach ($penggilingan->transports as $transport) {
                if ($transport->nota_timbang) {
                    $this->imageService->delete($transport->nota_timbang);
                }
            }

            $penggilingan->delete(); // Cascade will delete transports

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data penggilingan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data penggilingan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get summary statistics
     */
    public function summary(Request $request): JsonResponse
    {
        $query = Penggilingan::query();

        if ($request->has('tanggal_dari')) {
            $query->whereDate('tanggal_pengajuan', '>=', $request->tanggal_dari);
        }
        if ($request->has('tanggal_sampai')) {
            $query->whereDate('tanggal_pengajuan', '<=', $request->tanggal_sampai);
        }
        if ($request->has('nama_penggilingan')) {
            $query->where('nama_penggilingan', 'LIKE', '%' . $request->nama_penggilingan . '%');
        }

        $totalTonase = $query->sum('total_tonase');
        $totalAngkutan = $query->sum('jumlah_angkutan');
        $totalPengajuan = $query->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_tonase' => round($totalTonase, 3),
                'total_angkutan' => $totalAngkutan,
                'total_pengajuan' => $totalPengajuan
            ]
        ]);
    }

    /**
     * Export penggilingan to Excel
     */
    public function export(Request $request)
    {
        $filters = $request->only(['tanggal_dari', 'tanggal_sampai', 'nama_penggilingan', 'kabupaten']);
        
        return Excel::download(
            new PenggilinganExport($filters),
            'data_penggilingan_' . date('Y-m-d_His') . '.xlsx'
        );
    }
    
    /**
     * Export Makloon GKP data to Excel per penggilingan (Form Pengajuan GKP Maklon MPP)
     */
    public function exportMakloonGKP($id)
    {
        $penggilingan = Penggilingan::findOrFail($id);
        $year = date('Y');
        
        // Sanitize filename
        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $penggilingan->nama ?? 'penggilingan');
        
        return Excel::download(
            new MakloonGKPExport($id, $year),
            'form_gkp_maklon_' . $filename . '_' . $year . '.xlsx'
        );
    }

    /**
     * Verifikasi data makloon (SuperAdmin & Admin only)
     */
    public function verifikasi(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status_verifikasi' => 'required|in:pending,disetujui,ditolak',
            'catatan_verifikasi' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $penggilingan = Penggilingan::find($id);
        if (!$penggilingan) {
            return response()->json(['success' => false, 'message' => 'Data makloon tidak ditemukan'], 404);
        }

        $penggilingan->update([
            'status_verifikasi' => $request->status_verifikasi,
            'catatan_verifikasi' => $request->catatan_verifikasi,
            'verified_at' => $request->status_verifikasi !== 'pending' ? now() : null,
            'verified_by' => $request->status_verifikasi !== 'pending' ? $request->user()->id : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status verifikasi berhasil diperbarui',
            'data' => $penggilingan
        ]);
    }
}
