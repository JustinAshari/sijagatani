<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penggilingan extends Model
{
    protected $table = 'penggilingan';

    protected $fillable = [
        'tanggal_pengajuan',
        'nama_penggilingan',
        'komoditas',
        'foto_gkp_1',
        'foto_gkp_2',
        'lokasi_makloon',
        'provinsi_id',
        'kabupaten_id',
        'kecamatan_id',
        'kalurahan_id',
        'total_tonase',
        'jumlah_angkutan',
        'status_verifikasi',
        'catatan_verifikasi',
        'verified_at',
        'verified_by'
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'total_tonase' => 'decimal:3',
        'jumlah_angkutan' => 'integer',
        'verified_at' => 'datetime'
    ];

    protected $with = ['transports', 'provinsi', 'kabupaten', 'kecamatan', 'kalurahan']; // Eager load relations by default

    protected $appends = ['foto_gkp_1_url', 'foto_gkp_2_url'];

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }

    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_id');
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    public function kalurahan(): BelongsTo
    {
        return $this->belongsTo(Kalurahan::class, 'kalurahan_id');
    }

    // Removed petani relationship since we now store nama_petani as string
    // public function petani(): BelongsTo
    // {
    //     return $this->belongsTo(Petani::class);
    // }

    public function transports(): HasMany
    {
        return $this->hasMany(PenggilinganTransport::class)->orderBy('urutan');
    }

    // Auto calculate total_tonase and jumlah_angkutan from transports
    public function calculateTotals(): void
    {
        $this->total_tonase = $this->transports->sum('kuantum');
        $this->jumlah_angkutan = $this->transports->count();
        $this->save();
    }

    // Accessor untuk full URL foto GKP 1
    public function getFotoGkp1UrlAttribute(): ?string
    {
        if ($this->foto_gkp_1) {
            return url('storage/' . $this->foto_gkp_1);
        }
        return null;
    }

    // Accessor untuk full URL foto GKP 2
    public function getFotoGkp2UrlAttribute(): ?string
    {
        if ($this->foto_gkp_2) {
            return url('storage/' . $this->foto_gkp_2);
        }
        return null;
    }
}
