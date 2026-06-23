<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiPetani extends Model
{
    protected $table = 'transaksi_petani';

    protected $fillable = [
        'petani_id',
        'komoditas',
        'tanggal_transaksi',
        'volume_kg',
        'harga_per_kg',
        'nominal',
        'status_transaksi',
        'status_verifikasi',
        'catatan_verifikasi',
        'verified_at',
        'verified_by',
        'bank',
        'no_rekening',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'volume_kg' => 'decimal:2',
        'harga_per_kg' => 'decimal:2',
        'nominal' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function petani(): BelongsTo
    {
        return $this->belongsTo(Petani::class, 'petani_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
