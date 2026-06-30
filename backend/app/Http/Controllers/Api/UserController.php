<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::orderBy('created_at', 'desc');
        $perPageInput = $request->input('per_page', 'all');
        $allowedPerPage = [10, 20, 50, 100];

        if ($perPageInput === 'all') {
            $users = $query->get();

            return response()->json([
                'success' => true,
                'data' => $users,
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => $users->count(),
                    'per_page' => $users->count(),
                ],
            ]);
        }

        $perPage = (int) $perPageInput;
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $users = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'total' => $users->total(),
                'per_page' => $users->perPage(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['admin', 'lapangan', 'penggilingan'])],
            'nama_penggilingan' => 'nullable|string|max:255',
        ];

        $request->validate($rules);

        $userData = [
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ];

        // Wajib isi nama_penggilingan untuk role penggilingan
        if ($request->role === 'penggilingan') {
            if (empty($request->nama_penggilingan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama penggilingan wajib diisi untuk role Penggilingan',
                    'errors' => ['nama_penggilingan' => ['Nama penggilingan wajib diisi']]
                ], 422);
            }
            $userData['nama_penggilingan'] = $request->nama_penggilingan;
        }

        $user = User::create($userData);

        ActivityLogService::log($request, 'create', 'user', "Menambahkan akun: {$user->name} ({$user->username}), role: {$user->role}");

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role' => ['required', Rule::in(['admin', 'lapangan', 'penggilingan'])],
            'nama_penggilingan' => 'nullable|string|max:255',
        ]);

        // Wajib isi nama_penggilingan untuk role penggilingan
        if ($request->role === 'penggilingan' && empty($request->nama_penggilingan)) {
            return response()->json([
                'success' => false,
                'message' => 'Nama penggilingan wajib diisi untuk role Penggilingan',
                'errors' => ['nama_penggilingan' => ['Nama penggilingan wajib diisi']]
            ], 422);
        }

        $user->name = $request->name;
        $user->username = $request->username;
        $user->role = $request->role;
        $user->nama_penggilingan = $request->role === 'penggilingan' ? $request->nama_penggilingan : null;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        ActivityLogService::log($request, 'update', 'user', "Mengupdate akun: {$user->name} ({$user->username}), role: {$user->role}");

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diupdate',
            'data' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting superadmin
        if ($user->role === 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus akun superadmin'
            ], 403);
        }
        
        ActivityLogService::log($request, 'delete', 'user', "Menghapus akun: {$user->name} ({$user->username}), role: {$user->role}");

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ]);
    }
}
