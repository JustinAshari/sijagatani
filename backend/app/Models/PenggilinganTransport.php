<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenggilinganTransport extends Model
{
    protected $table = 'penggilingan_transport';

    protected $fillable = [
        'penggilingan_id',
        'urutan',
        'nama_pengemudi',
        'nopol',
        'kuantum',
        'nota_timbang',
        'surat_jalan'
    ];

    protected $casts = [
        'kuantum' => 'decimal:3'
    ];

    public function penggilingan(): BelongsTo
    {
        return $this->belongsTo(Penggilingan::class);
    }
}
