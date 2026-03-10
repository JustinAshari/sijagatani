<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'nama_penggilingan', // Hanya diisi untuk role 'penggilingan'
        'parent_id',         // Diisi untuk sub-admin penggilingan
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is superadmin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is admin lapangan Bulog (role lapangan)
     * Bisa mengelola (tambah, edit, hapus) data petani
     */
    public function isAdminLapangan(): bool
    {
        return $this->role === 'lapangan';
    }

    /**
     * Check if user is admin pekerja penggilingan (role penggilingan)
     * Berlaku untuk parent admin maupun sub-admin penggilingan
     */
    public function isAdminPenggilingan(): bool
    {
        return $this->role === 'penggilingan';
    }

    /**
     * Check if this is the main/parent penggilingan account (not a sub-admin)
     */
    public function isParentPenggilingan(): bool
    {
        return $this->role === 'penggilingan' && is_null($this->parent_id);
    }

    /**
     * Check if this is a sub-admin of a penggilingan company
     */
    public function isSubAdmin(): bool
    {
        return $this->role === 'penggilingan' && !is_null($this->parent_id);
    }

    /**
     * Relationship: parent account (for sub-admins)
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Relationship: sub-admin accounts (for parent penggilingan)
     */
    public function subAdmins()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /**
     * Check if user can manage petani data
     * SuperAdmin, Admin, and Admin Lapangan (lapangan role) can manage
     */
    public function canManagePetani(): bool
    {
        return in_array($this->role, ['superadmin', 'admin', 'lapangan']);
    }

    /**
     * Check if user can manage penggilingan data
     * SuperAdmin, Admin, and Admin Penggilingan (penggilingan role) can manage
     */
    public function canManagePenggilingan(): bool
    {
        return in_array($this->role, ['superadmin', 'admin', 'penggilingan']);
    }

    /**
     * Get human readable role name
     */
    public function getRoleName(): string
    {
        return match($this->role) {
            'superadmin' => 'Super Admin',
            'admin' => 'Admin',
            'lapangan' => 'Admin Lapangan Bulog',
            'penggilingan' => 'Admin Penggilingan',
            default => ucfirst($this->role)
        };
    }
}
