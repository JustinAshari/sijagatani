<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Return paginated activity logs (superadmin only).
     */
    public function index(Request $request): JsonResponse
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('username', 'LIKE', "%{$s}%")
                  ->orWhere('name', 'LIKE', "%{$s}%")
                  ->orWhere('description', 'LIKE', "%{$s}%")
                  ->orWhere('module', 'LIKE', "%{$s}%")
                  ->orWhere('action', 'LIKE', "%{$s}%");
            });
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        $perPageInput = $request->input('per_page', 'all');
        $allowedPerPage = [10, 20, 50, 100];

        if ($perPageInput === 'all') {
            $logs = $query->get();

            return response()->json([
                'success' => true,
                'data' => $logs,
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => $logs->count(),
                    'per_page' => $logs->count(),
                ],
            ]);
        }

        $perPage = (int) $perPageInput;
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $logs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $logs->items(),
            'meta'    => [
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
                'total'        => $logs->total(),
                'per_page'     => $logs->perPage(),
            ],
        ]);
    }
}
