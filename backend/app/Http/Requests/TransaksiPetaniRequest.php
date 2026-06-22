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
            'tanggal_transaksi' => ['required', 'date'],
            'volume_kg' => ['required', 'numeric', 'min:0.01'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'status_transaksi' => ['required', 'in:pending,sukses,gagal'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'petani_id' => 'Nama Petani',
            'tanggal_transaksi' => 'Tanggal Transaksi',
            'volume_kg' => 'Volume (KG)',
            'nominal' => 'Nominal (Rp)',
            'status_transaksi' => 'Status Transaksi',
        ];
    }
}
