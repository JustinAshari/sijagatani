<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * SubAdminController
 * 
 * Allows a parent penggilingan admin to manage sub-admins for their company.
 * Only accessible by users with role=penggilingan AND parent_id=NULL.
 */
class SubAdminController extends Controller
{
    /**
     * Ensure the authenticated user is a parent penggilingan (not a sub-admin).
     */
    private function authorizeParent(Request $request)
    {
        $user = $request->user();
        if (!$user->isParentPenggilingan()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Hanya admin utama penggilingan yang dapat mengelola sub-admin.'
            ], 403));
        }
        return $user;
    }

    /**
     * GET /my-sub-admins
     * List all sub-admins belonging to the authenticated parent penggilingan.
     */
    public function index(Request $request)
    {
        $parent = $this->authorizeParent($request);

        $subAdmins = $parent->subAdmins()
            ->orderBy('created_at', 'desc')
            ->get(['id', 'name', 'username', 'role', 'nama_penggilingan', 'parent_id', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => $subAdmins
        ]);
    }

    /**
     * POST /my-sub-admins
     * Create a new sub-admin for the authenticated parent penggilingan.
     */
    public function store(Request $request)
    {
        $parent = $this->authorizeParent($request);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $subAdmin = User::create([
            'name'              => $request->name,
            'username'          => $request->username,
            'password'          => Hash::make($request->password),
            'role'              => 'penggilingan',
            'nama_penggilingan' => $parent->nama_penggilingan,
            'parent_id'         => $parent->id,
        ]);

        ActivityLogService::log($request, 'create', 'sub-admin', "Menambahkan sub-admin: {$subAdmin['name']} ({$subAdmin['username']}) untuk {$parent->nama_penggilingan}");

        return response()->json([
            'success' => true,
            'message' => 'Sub-admin berhasil ditambahkan',
            'data' => $subAdmin->only(['id', 'name', 'username', 'role', 'nama_penggilingan', 'parent_id', 'created_at'])
        ], 201);
    }

    /**
     * PUT /my-sub-admins/{id}
     * Update a sub-admin (only if it belongs to the authenticated parent).
     */
    public function update(Request $request, string $id)
    {
        $parent = $this->authorizeParent($request);

        $subAdmin = User::where('id', $id)
            ->where('parent_id', $parent->id)
            ->firstOrFail();

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($subAdmin->id)],
            'password' => 'nullable|string|min:6',
        ]);

        $subAdmin->name     = $request->name;
        $subAdmin->username = $request->username;

        if ($request->filled('password')) {
            $subAdmin->password = Hash::make($request->password);
        }

        $subAdmin->save();

        ActivityLogService::log($request, 'update', 'sub-admin', "Mengupdate sub-admin: {$subAdmin->name} ({$subAdmin->username})");

        return response()->json([
            'success' => true,
            'message' => 'Sub-admin berhasil diupdate',
            'data' => $subAdmin->only(['id', 'name', 'username', 'role', 'nama_penggilingan', 'parent_id', 'created_at'])
        ]);
    }

    /**
     * DELETE /my-sub-admins/{id}
     * Delete a sub-admin (only if it belongs to the authenticated parent).
     */
    public function destroy(Request $request, string $id)
    {
        $parent = $this->authorizeParent($request);

        $subAdmin = User::where('id', $id)
            ->where('parent_id', $parent->id)
            ->firstOrFail();

        ActivityLogService::log($request, 'delete', 'sub-admin', "Menghapus sub-admin: {$subAdmin->name} ({$subAdmin->username})");

        $subAdmin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sub-admin berhasil dihapus'
        ]);
    }
}
