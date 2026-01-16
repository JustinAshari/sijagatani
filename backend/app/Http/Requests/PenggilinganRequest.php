<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PenggilinganRequest extends FormRequest
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
            'tanggal' => ['required', 'date'],
            'petani_id' => ['required', 'exists:petani,id'],
            'jumlah_gabah' => ['required', 'numeric', 'min:0'],
            'hasil_beras' => ['nullable', 'numeric', 'min:0'],
            'biaya_giling' => ['nullable', 'numeric', 'min:0'],
            'harga_per_kg' => ['nullable', 'numeric', 'min:0'],
            'total_pembayaran' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:Pending,Proses,Selesai,Dibayar'],
            'keterangan' => ['nullable', 'string'],
            'foto_proses' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
            'bukti_bayar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,pdf', 'max:5120']
        ];
    }
}
