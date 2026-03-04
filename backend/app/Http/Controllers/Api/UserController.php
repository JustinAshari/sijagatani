<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $users
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

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diupdate',
            'data' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting superadmin
        if ($user->role === 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus akun superadmin'
            ], 403);
        }
        
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ]);
    }
}
