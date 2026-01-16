<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Petani extends Model
{
    protected $table = 'petani';

    protected $fillable = [
        'tanggal',
        'nik',
        'nama',
        'alamat',
        'kabupaten',
        'kecamatan',
        'desa',
        'no_telepon',
        'bank',
        'no_rekening',
        'luas_lahan',
        'alamat_lahan',
        'potensi_panen',
        'komoditi',
        'foto_ktp',
        'foto_petani',
        'foto_komoditi',
        'kwitansi_pembayaran'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'luas_lahan' => 'decimal:2',
        'potensi_panen' => 'decimal:2'
    ];

    public function penggilingan(): HasMany
    {
        return $this->hasMany(Penggilingan::class);
    }
}
