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

    protected $appends = ['nota_timbang_url', 'surat_jalan_url'];

    public function penggilingan(): BelongsTo
    {
        return $this->belongsTo(Penggilingan::class);
    }

    // Accessor untuk full URL nota timbang
    public function getNotaTimbangUrlAttribute(): ?string
    {
        if ($this->nota_timbang) {
            return url('storage/' . $this->nota_timbang);
        }
        return null;
    }

    // Accessor untuk full URL surat jalan
    public function getSuratJalanUrlAttribute(): ?string
    {
        if ($this->surat_jalan) {
            return url('storage/' . $this->surat_jalan);
        }
        return null;
    }
}
