<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TransaksiPetani;
use App\Models\Petani;
use App\Http\Requests\TransaksiPetaniRequest;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransaksiPetaniController extends Controller
{
    /**
     * Display a listing of the resource with filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = TransaksiPetani::with(['petani', 'verifier']);

        // Filter by tanggal_transaksi
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
        }

        // Filter by status_transaksi
        if ($request->filled('status_transaksi')) {
            $query->where('status_transaksi', $request->status_transaksi);
        }

        // Filter by status_verifikasi
        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        // Filter by komoditas (commodity)
        if ($request->filled('komoditas')) {
            $query->where('komoditas', $request->komoditas);
        }

        // Filter by provinsi_id
        if ($request->filled('provinsi_id')) {
            $query->whereHas('petani', function ($q) use ($request) {
                $q->where('provinsi_id', $request->provinsi_id);
            });
        }

        // Filter by kabupaten_id
        if ($request->filled('kabupaten_id')) {
            $query->whereHas('petani', function ($q) use ($request) {
                $q->where('kabupaten_id', $request->kabupaten_id);
            });
        }

        // Filter by kecamatan_id
        if ($request->filled('kecamatan_id')) {
            $query->whereHas('petani', function ($q) use ($request) {
                $q->where('kecamatan_id', $request->kecamatan_id);
            });
        }

        // Filter by kalurahan_id
        if ($request->filled('kalurahan_id')) {
            $query->whereHas('petani', function ($q) use ($request) {
                $q->where('kalurahan_id', $request->kalurahan_id);
            });
        }

        // Search by nama or NIK of petani
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('petani', function ($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        $query->latest('tanggal_transaksi')->latest('id');

        $perPageInput = $request->input('per_page', 'all');
        $allowedPerPage = [10, 20, 50, 100];

        if ($perPageInput === 'all') {
            $transaksi = $query->get();

            return response()->json([
                'success' => true,
                'data' => $transaksi,
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => $transaksi->count(),
                    'per_page' => $transaksi->count(),
                ],
            ]);
        }

        $perPage = (int) $perPageInput;
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $transaksi = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $transaksi->items(),
            'meta' => [
                'current_page' => $transaksi->currentPage(),
                'last_page' => $transaksi->lastPage(),
                'total' => $transaksi->total(),
                'per_page' => $transaksi->perPage(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransaksiPetaniRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Default bank and no_rekening from farmer if empty
            if (empty($data['bank']) || empty($data['no_rekening'])) {
                $petani = Petani::find($data['petani_id']);
                if ($petani) {
                    if (empty($data['bank'])) {
                        $data['bank'] = $petani->bank;
                    }
                    if (empty($data['no_rekening'])) {
                        $data['no_rekening'] = $petani->no_rekening;
                    }
                }
            }

            $transaksi = TransaksiPetani::create($data);

            // Load petani for activity log description
            $petani = Petani::find($transaksi->petani_id);
            $formattedNominal = number_format($transaksi->nominal, 0, ',', '.');
            
            ActivityLogService::log(
                $request, 
                'create', 
                'transaksi-petani', 
                "Menambahkan transaksi petani: {$petani->nama} (NIK: {$petani->nik}) senilai Rp {$formattedNominal} dengan volume {$transaksi->volume_kg} KG"
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi petani berhasil ditambahkan',
                'data' => $transaksi->load('petani')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan transaksi petani',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $transaksi = TransaksiPetani::with(['petani'])->find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi petani tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transaksi
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransaksiPetaniRequest $request, string $id): JsonResponse
    {
        try {
            $transaksi = TransaksiPetani::find($id);

            if (!$transaksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi petani tidak ditemukan'
                ], 404);
            }

            $data = $request->validated();

            // Default bank and no_rekening from farmer if empty
            if (empty($data['bank']) || empty($data['no_rekening'])) {
                $petani = Petani::find($data['petani_id'] ?? $transaksi->petani_id);
                if ($petani) {
                    if (empty($data['bank'])) {
                        $data['bank'] = $petani->bank;
                    }
                    if (empty($data['no_rekening'])) {
                        $data['no_rekening'] = $petani->no_rekening;
                    }
                }
            }

            $transaksi->update($data);

            // Load petani for activity log description
            $petani = Petani::find($transaksi->petani_id);
            $formattedNominal = number_format($transaksi->nominal, 0, ',', '.');
            
            ActivityLogService::log(
                $request, 
                'update', 
                'transaksi-petani', 
                "Mengupdate transaksi petani: {$petani->nama} (NIK: {$petani->nik}) senilai Rp {$formattedNominal} dengan volume {$transaksi->volume_kg} KG"
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi petani berhasil diperbarui',
                'data' => $transaksi->load('petani')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui transaksi petani',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $transaksi = TransaksiPetani::find($id);

            if (!$transaksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi petani tidak ditemukan'
                ], 404);
            }

            $petani = Petani::find($transaksi->petani_id);
            $formattedNominal = number_format($transaksi->nominal, 0, ',', '.');

            ActivityLogService::log(
                $request, 
                'delete', 
                'transaksi-petani', 
                "Menghapus transaksi petani: {$petani->nama} (NIK: {$petani->nik}) senilai Rp {$formattedNominal}"
            );

            $transaksi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi petani berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi petani',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifikasi transaksi petani (SuperAdmin & Admin only)
     */
    public function verifikasi(Request $request, string $id): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
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

        $transaksi = TransaksiPetani::find($id);
        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Transaksi petani tidak ditemukan'], 404);
        }

        $updateData = [
            'status_verifikasi' => $request->status_verifikasi,
            'catatan_verifikasi' => $request->catatan_verifikasi,
            'verified_at' => $request->status_verifikasi !== 'pending' ? now() : null,
            'verified_by' => $request->status_verifikasi !== 'pending' ? $request->user()->id : null,
        ];

        if ($request->status_verifikasi === 'ditolak') {
            $updateData['status_transaksi'] = 'ditolak';
        } elseif ($request->status_verifikasi === 'disetujui' && $transaksi->status_transaksi === 'ditolak') {
            $updateData['status_transaksi'] = 'belum';
        }

        $transaksi->update($updateData);

        $transaksi->load(['petani', 'verifier']);

        $formattedNominal = number_format($transaksi->nominal, 0, ',', '.');
        ActivityLogService::log(
            $request, 
            'verify', 
            'transaksi-petani', 
            "Verifikasi transaksi petani {$transaksi->petani->nama} senilai Rp {$formattedNominal} → {$request->status_verifikasi}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Status verifikasi transaksi berhasil diperbarui',
            'data' => $transaksi
        ]);
    }
}
