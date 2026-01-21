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
        'email',
        'password',
        'role',
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
            'email_verified_at' => 'datetime',
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
     * Bisa mengelola (tambah, edit, hapus) data makloon/penggilingan
     */
    public function isAdminPenggilingan(): bool
    {
        return $this->role === 'penggilingan';
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
