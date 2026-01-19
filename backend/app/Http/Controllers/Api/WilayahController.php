<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kalurahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WilayahController extends Controller
{
    // ==================== PROVINSI ====================
    public function getProvinsi()
    {
        $provinsi = Provinsi::withCount('kabupaten')->orderBy('nama')->get();
        return response()->json(['success' => true, 'data' => $provinsi]);
    }

    public function storeProvinsi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:100|unique:provinsi,nama'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $provinsi = Provinsi::create(['nama' => $request->nama]);
        
        return response()->json([
            'success' => true,
            'message' => 'Provinsi berhasil ditambahkan',
            'data' => $provinsi
        ], 201);
    }

    public function updateProvinsi(Request $request, $id)
    {
        $provinsi = Provinsi::find($id);
        if (!$provinsi) {
            return response()->json(['success' => false, 'message' => 'Provinsi tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:100|unique:provinsi,nama,' . $id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $provinsi->update(['nama' => $request->nama]);
        
        return response()->json([
            'success' => true,
            'message' => 'Provinsi berhasil diupdate',
            'data' => $provinsi
        ]);
    }

    public function deleteProvinsi($id)
    {
        $provinsi = Provinsi::find($id);
        if (!$provinsi) {
            return response()->json(['success' => false, 'message' => 'Provinsi tidak ditemukan'], 404);
        }

        $provinsi->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Provinsi berhasil dihapus'
        ]);
    }

    // ==================== KABUPATEN ====================
    public function getKabupaten(Request $request)
    {
        $query = Kabupaten::with('provinsi')->withCount('kecamatan');
        
        if ($request->has('provinsi_id')) {
            $query->where('provinsi_id', $request->provinsi_id);
        }
        
        $kabupaten = $query->orderBy('nama')->get();
        return response()->json(['success' => true, 'data' => $kabupaten]);
    }

    public function storeKabupaten(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provinsi_id' => 'required|exists:provinsi,id',
            'nama' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $kabupaten = Kabupaten::create($request->only(['provinsi_id', 'nama']));
        $kabupaten->load('provinsi');
        
        return response()->json([
            'success' => true,
            'message' => 'Kabupaten berhasil ditambahkan',
            'data' => $kabupaten
        ], 201);
    }

    public function updateKabupaten(Request $request, $id)
    {
        $kabupaten = Kabupaten::find($id);
        if (!$kabupaten) {
            return response()->json(['success' => false, 'message' => 'Kabupaten tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'provinsi_id' => 'required|exists:provinsi,id',
            'nama' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $kabupaten->update($request->only(['provinsi_id', 'nama']));
        $kabupaten->load('provinsi');
        
        return response()->json([
            'success' => true,
            'message' => 'Kabupaten berhasil diupdate',
            'data' => $kabupaten
        ]);
    }

    public function deleteKabupaten($id)
    {
        $kabupaten = Kabupaten::find($id);
        if (!$kabupaten) {
            return response()->json(['success' => false, 'message' => 'Kabupaten tidak ditemukan'], 404);
        }

        $kabupaten->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Kabupaten berhasil dihapus'
        ]);
    }

    // ==================== KECAMATAN ====================
    public function getKecamatan(Request $request)
    {
        $query = Kecamatan::with(['kabupaten.provinsi'])->withCount('kalurahan');
        
        if ($request->has('kabupaten_id')) {
            $query->where('kabupaten_id', $request->kabupaten_id);
        }
        
        $kecamatan = $query->orderBy('nama')->get();
        return response()->json(['success' => true, 'data' => $kecamatan]);
    }

    public function storeKecamatan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kabupaten_id' => 'required|exists:kabupaten,id',
            'nama' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $kecamatan = Kecamatan::create($request->only(['kabupaten_id', 'nama']));
        $kecamatan->load(['kabupaten.provinsi']);
        
        return response()->json([
            'success' => true,
            'message' => 'Kecamatan berhasil ditambahkan',
            'data' => $kecamatan
        ], 201);
    }

    public function updateKecamatan(Request $request, $id)
    {
        $kecamatan = Kecamatan::find($id);
        if (!$kecamatan) {
            return response()->json(['success' => false, 'message' => 'Kecamatan tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'kabupaten_id' => 'required|exists:kabupaten,id',
            'nama' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $kecamatan->update($request->only(['kabupaten_id', 'nama']));
        $kecamatan->load(['kabupaten.provinsi']);
        
        return response()->json([
            'success' => true,
            'message' => 'Kecamatan berhasil diupdate',
            'data' => $kecamatan
        ]);
    }

    public function deleteKecamatan($id)
    {
        $kecamatan = Kecamatan::find($id);
        if (!$kecamatan) {
            return response()->json(['success' => false, 'message' => 'Kecamatan tidak ditemukan'], 404);
        }

        $kecamatan->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Kecamatan berhasil dihapus'
        ]);
    }

    // ==================== KALURAHAN ====================
    public function getKalurahan(Request $request)
    {
        $query = Kalurahan::with(['kecamatan.kabupaten.provinsi']);
        
        if ($request->has('kecamatan_id')) {
            $query->where('kecamatan_id', $request->kecamatan_id);
        }
        
        $kalurahan = $query->orderBy('nama')->get();
        return response()->json(['success' => true, 'data' => $kalurahan]);
    }

    public function storeKalurahan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kecamatan_id' => 'required|exists:kecamatan,id',
            'nama' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $kalurahan = Kalurahan::create($request->only(['kecamatan_id', 'nama']));
        $kalurahan->load(['kecamatan.kabupaten.provinsi']);
        
        return response()->json([
            'success' => true,
            'message' => 'Kalurahan berhasil ditambahkan',
            'data' => $kalurahan
        ], 201);
    }

    public function updateKalurahan(Request $request, $id)
    {
        $kalurahan = Kalurahan::find($id);
        if (!$kalurahan) {
            return response()->json(['success' => false, 'message' => 'Kalurahan tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'kecamatan_id' => 'required|exists:kecamatan,id',
            'nama' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $kalurahan->update($request->only(['kecamatan_id', 'nama']));
        $kalurahan->load(['kecamatan.kabupaten.provinsi']);
        
        return response()->json([
            'success' => true,
            'message' => 'Kalurahan berhasil diupdate',
            'data' => $kalurahan
        ]);
    }

    public function deleteKalurahan($id)
    {
        $kalurahan = Kalurahan::find($id);
        if (!$kalurahan) {
            return response()->json(['success' => false, 'message' => 'Kalurahan tidak ditemukan'], 404);
        }

        $kalurahan->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Kalurahan berhasil dihapus'
        ]);
    }
}
