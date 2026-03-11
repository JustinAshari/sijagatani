<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogService
{
    /**
     * Record an activity log entry.
     *
     * @param  Request       $request
     * @param  string        $action      e.g. login, logout, create, update, delete, verify
     * @param  string        $module      e.g. petani, penggilingan, user, wilayah, sub-admin
     * @param  string|null   $description Human-readable description
     */
    public static function log(Request $request, string $action, string $module, ?string $description = null): void
    {
        $user = $request->user();

        ActivityLog::create([
            'user_id'     => $user?->id,
            'username'    => $user?->username,
            'name'        => $user?->name,
            'role'        => $user?->role,
            'action'      => $action,
            'module'      => $module,
            'description' => $description,
            'ip_address'  => $request->ip(),
        ]);
    }

    /**
     * Record a login/logout event where the user object is provided explicitly
     * (auth guard may not be set yet at that point).
     */
    public static function logUser(Request $request, $user, string $action, string $module, ?string $description = null): void
    {
        ActivityLog::create([
            'user_id'     => $user?->id,
            'username'    => $user?->username,
            'name'        => $user?->name,
            'role'        => $user?->role,
            'action'      => $action,
            'module'      => $module,
            'description' => $description,
            'ip_address'  => $request->ip(),
        ]);
    }
}
