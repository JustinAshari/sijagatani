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
        $query = TransaksiPetani::with(['petani']);

        // Filter by tanggal_transaksi
        if ($request->has('tanggal_dari')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
        }
        if ($request->has('tanggal_sampai')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
        }

        // Filter by status_transaksi
        if ($request->has('status_transaksi') && $request->status_transaksi !== '') {
            $query->where('status_transaksi', $request->status_transaksi);
        }

        // Search by nama or NIK of petani
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('petani', function ($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        $transaksi = $query->latest('tanggal_transaksi')->latest('id')->get();

        return response()->json([
            'success' => true,
            'data' => $transaksi
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransaksiPetaniRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
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
}
