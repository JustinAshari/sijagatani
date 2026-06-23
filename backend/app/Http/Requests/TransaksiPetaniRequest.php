<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransaksiPetaniRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'petani_id' => ['required', 'exists:petani,id'],
            'komoditas' => ['required', 'string', 'max:255'],
            'tanggal_transaksi' => ['required', 'date'],
            'volume_kg' => ['required', 'numeric', 'min:0.01'],
            'harga_per_kg' => ['required', 'numeric', 'min:0.01'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'status_transaksi' => ['required', 'in:belum,sudah,ditolak'],
            'bank' => ['nullable', 'string', 'max:255'],
            'no_rekening' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'petani_id' => 'Nama Petani',
            'komoditas' => 'Komoditas',
            'tanggal_transaksi' => 'Tanggal Transaksi',
            'volume_kg' => 'Volume (KG)',
            'harga_per_kg' => 'Harga per KG',
            'nominal' => 'Nominal (Rp)',
            'status_transaksi' => 'Status Transaksi',
            'bank' => 'Bank',
            'no_rekening' => 'Nomor Rekening',
        ];
    }
}
