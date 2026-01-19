<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PetaniRequest extends FormRequest
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
        $petaniId = $this->route('petani') ? $this->route('petani') : null;
        
        return [
            'tanggal' => ['required', 'date'],
            'nik' => ['required', 'string', 'size:16', 'unique:petani,nik,' . $petaniId],
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'provinsi_id' => ['nullable', 'exists:provinsi,id'],
            'kabupaten_id' => ['nullable', 'exists:kabupaten,id'],
            'kecamatan_id' => ['nullable', 'exists:kecamatan,id'],
            'kalurahan_id' => ['nullable', 'exists:kalurahan,id'],
            'bank' => ['nullable', 'string', 'max:100'],
            'no_rekening' => ['nullable', 'string', 'max:50'],
            'no_telepon' => ['nullable', 'string', 'max:15'],
            'luas_lahan' => ['required', 'numeric', 'min:0'],
            'alamat_lahan' => ['required', 'string'],
            'potensi_panen' => ['required', 'numeric', 'min:0'],
            'komoditi' => ['required', 'in:Gabah,Jagung,Beras'],
            'foto_ktp' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
            'foto_petani' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
            'foto_komoditi' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
            'kwitansi_pembayaran' => ['nullable', 'mimes:jpeg,png,jpg,pdf', 'max:5120']
        ];
    }
}
