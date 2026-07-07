<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Petani;
use App\Http\Requests\PetaniRequest;
use App\Services\ActivityLogService;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PetaniController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource with filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Petani::with(['provinsi', 'kabupaten', 'kecamatan', 'kalurahan']);

        // Filter by provinsi_id
        if ($request->filled('provinsi_id')) {
            $query->where('provinsi_id', $request->provinsi_id);
        }

        // Filter by kabupaten_id
        if ($request->filled('kabupaten_id')) {
            $query->where('kabupaten_id', $request->kabupaten_id);
        }

        // Filter by kecamatan_id
        if ($request->filled('kecamatan_id')) {
            $query->where('kecamatan_id', $request->kecamatan_id);
        }

        // Filter by kalurahan_id
        if ($request->filled('kalurahan_id')) {
            $query->where('kalurahan_id', $request->kalurahan_id);
        }

        // Filter by komoditi
        if ($request->filled('komoditi')) {
            $query->where('komoditi', $request->komoditi);
        }

        // Filter by tanggal (tanggal field, not created_at)
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        // Search by nama or NIK
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        $query->latest();

        $perPageInput = $request->input('per_page', 'all');
        $allowedPerPage = [10, 20, 50, 100];

        if ($perPageInput === 'all') {
            $petani = $query->get();

            return response()->json([
                'success' => true,
                'data' => $petani,
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => $petani->count(),
                    'per_page' => $petani->count(),
                ],
            ]);
        }

        $perPage = (int) $perPageInput;
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $petani = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $petani->items(),
            'meta' => [
                'current_page' => $petani->currentPage(),
                'last_page' => $petani->lastPage(),
                'total' => $petani->total(),
                'per_page' => $petani->perPage(),
            ],
        ]);
    }

    /**
     * Check if NIK already exists
     */
    public function checkNik(Request $request): JsonResponse
    {
        $nik = $request->input('nik');
        $petaniId = $request->input('petani_id'); // For update check

        $query = Petani::where('nik', $nik);
        
        if ($petaniId) {
            $query->where('id', '!=', $petaniId);
        }

        $exists = $query->exists();

        if ($exists) {
            $existingPetani = $query->first();
            return response()->json([
                'success' => false,
                'exists' => true,
                'message' => 'NIK sudah terdaftar',
                'data' => [
                    'nama' => $existingPetani->nama,
                    'kabupaten' => $existingPetani->kabupaten,
                    'kecamatan' => $existingPetani->kecamatan
                ]
            ], 200);
        }

        return response()->json([
            'success' => true,
            'exists' => false,
            'message' => 'NIK tersedia'
        ]);
    }

    public function store(PetaniRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Handle multiple image uploads
            if ($request->hasFile('foto_ktp')) {
                $data['foto_ktp'] = $this->imageService->uploadAndCompress(
                    $request->file('foto_ktp'),
                    'petani/ktp',
                    600,
                    70
                );
            }

            if ($request->hasFile('foto_petani')) {
                $data['foto_petani'] = $this->imageService->uploadAndCompress(
                    $request->file('foto_petani'),
                    'petani/foto',
                    600,
                    70
                );
            }

            if ($request->hasFile('foto_komoditi')) {
                $data['foto_komoditi'] = $this->imageService->uploadAndCompress(
                    $request->file('foto_komoditi'),
                    'petani/komoditi',
                    800,
                    70
                );
            }

            if ($request->hasFile('kwitansi_pembayaran')) {
                $data['kwitansi_pembayaran'] = $this->imageService->uploadImageOrFile(
                    $request->file('kwitansi_pembayaran'),
                    'petani/kwitansi',
                    800,
                    70
                );
            }
            $petani = Petani::create($data);

            ActivityLogService::log($request, 'create', 'petani', "Menambahkan data petani: {$petani->nama} (NIK: {$petani->nik})");

            return response()->json([
                'success' => true,
                'message' => 'Data petani berhasil ditambahkan',
                'data' => $petani
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data petani',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $petani = Petani::with(['provinsi', 'kabupaten', 'kecamatan', 'kalurahan'])->find($id);

        if (!$petani) {
            return response()->json([
                'success' => false,
                'message' => 'Data petani tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $petani
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PetaniRequest $request, string $id): JsonResponse
    {
        try {
            $petani = Petani::find($id);

            if (!$petani) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data petani tidak ditemukan'
                ], 404);
            }

            $data = $request->validated();

            // Handle multiple image uploads
            if ($request->hasFile('foto_ktp')) {
                if ($petani->foto_ktp) {
                    $this->imageService->delete($petani->foto_ktp);
                }
                $data['foto_ktp'] = $this->imageService->uploadAndCompress(
                    $request->file('foto_ktp'),
                    'petani/ktp',
                    600,
                    70
                );
            }

            if ($request->hasFile('foto_petani')) {
                if ($petani->foto_petani) {
                    $this->imageService->delete($petani->foto_petani);
                }
                $data['foto_petani'] = $this->imageService->uploadAndCompress(
                    $request->file('foto_petani'),
                    'petani/foto',
                    600,
                    70
                );
            }

            if ($request->hasFile('foto_komoditi')) {
                if ($petani->foto_komoditi) {
                    $this->imageService->delete($petani->foto_komoditi);
                }
                $data['foto_komoditi'] = $this->imageService->uploadAndCompress(
                    $request->file('foto_komoditi'),
                    'petani/komoditi',
                    800,
                    70
                );
            }

            if ($request->hasFile('kwitansi_pembayaran')) {
                if ($petani->kwitansi_pembayaran) {
                    $this->imageService->delete($petani->kwitansi_pembayaran);
                }
                $data['kwitansi_pembayaran'] = $this->imageService->uploadImageOrFile(
                    $request->file('kwitansi_pembayaran'),
                    'petani/kwitansi',
                    800,
                    70
                );
            }
            $petani->update($data);

            ActivityLogService::log($request, 'update', 'petani', "Mengupdate data petani: {$petani->nama} (NIK: {$petani->nik})");

            return response()->json([
                'success' => true,
                'message' => 'Data petani berhasil diupdate',
                'data' => $petani
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data petani',
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
            $petani = Petani::find($id);

            if (!$petani) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data petani tidak ditemukan'
                ], 404);
            }

            // Delete images
            if ($petani->foto_ktp) {
                $this->imageService->delete($petani->foto_ktp);
            }
            if ($petani->foto_petani) {
                $this->imageService->delete($petani->foto_petani);
            }
            if ($petani->foto_komoditi) {
                $this->imageService->delete($petani->foto_komoditi);
            }
            if ($petani->kwitansi_pembayaran) {
                $this->imageService->delete($petani->kwitansi_pembayaran);
            }
            ActivityLogService::log($request, 'delete', 'petani', "Menghapus data petani: {$petani->nama} (NIK: {$petani->nik})");

            $petani->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data petani berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data petani',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export data petani to Excel
     */
    public function export(Request $request)
    {
        $filters = $request->only([
            'provinsi_id',
            'kabupaten_id',
            'kecamatan_id',
            'kalurahan_id',
            'komoditi',
            'tanggal_dari',
            'tanggal_sampai'
        ]);
        
        $filename = 'data_petani_' . date('Y-m-d_His') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PetaniExport($filters),
            $filename
        );
    }

}
