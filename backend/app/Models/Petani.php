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
        'kwitansi_pembayaran',
        'surat_pernyataan'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'luas_lahan' => 'decimal:2',
        'potensi_panen' => 'decimal:2'
    ];

    protected $appends = ['foto_ktp_url', 'foto_petani_url', 'foto_komoditi_url', 'kwitansi_pembayaran_url', 'surat_pernyataan_url'];

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

    // Accessor untuk full URL foto KTP
    public function getFotoKtpUrlAttribute(): ?string
    {
        if ($this->foto_ktp) {
            return url('storage/' . $this->foto_ktp);
        }
        return null;
    }

    // Accessor untuk full URL foto petani
    public function getFotoPetaniUrlAttribute(): ?string
    {
        if ($this->foto_petani) {
            return url('storage/' . $this->foto_petani);
        }
        return null;
    }

    // Accessor untuk full URL foto komoditi
    public function getFotoKomoditiUrlAttribute(): ?string
    {
        if ($this->foto_komoditi) {
            return url('storage/' . $this->foto_komoditi);
        }
        return null;
    }

    // Accessor untuk full URL kwitansi pembayaran
    public function getKwitansiPembayaranUrlAttribute(): ?string
    {
        if ($this->kwitansi_pembayaran) {
            return url('storage/' . $this->kwitansi_pembayaran);
        }
        return null;
    }

    // Accessor untuk full URL surat pernyataan
    public function getSuratPernyataanUrlAttribute(): ?string
    {
        if ($this->surat_pernyataan) {
            return url('storage/' . $this->surat_pernyataan);
        }
        return null;
    }
}
