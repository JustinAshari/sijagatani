<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Petani extends Model
{
    protected $table = 'petani';

    protected $fillable = [
        'tanggal',
        'nik',
        'nama',
        'alamat',
        'provinsi_id',
        'kabupaten_id',
        'kecamatan_id',
        'kalurahan_id',
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

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class);
    }

    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class);
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function kalurahan(): BelongsTo
    {
        return $this->belongsTo(Kalurahan::class);
    }
}
