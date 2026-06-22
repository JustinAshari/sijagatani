<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiPetani extends Model
{
    protected $table = 'transaksi_petani';

    protected $fillable = [
        'petani_id',
        'tanggal_transaksi',
        'volume_kg',
        'nominal',
        'status_transaksi',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'volume_kg' => 'decimal:2',
        'nominal' => 'decimal:2',
    ];

    public function petani(): BelongsTo
    {
        return $this->belongsTo(Petani::class, 'petani_id');
    }
}
