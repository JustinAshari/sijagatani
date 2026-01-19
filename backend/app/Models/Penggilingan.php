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
        'nama_petani',
        'nama_penggilingan',
        'foto_gkp_1',
        'foto_gkp_2',
        'lokasi_makloon',
        'total_tonase',
        'jumlah_angkutan'
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'total_tonase' => 'decimal:3',
        'jumlah_angkutan' => 'integer'
    ];

    protected $with = ['transports']; // Eager load transports by default

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
}
