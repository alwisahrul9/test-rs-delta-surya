<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    /**
     * Izinkan semua dokter yang login untuk melakukan update.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Aturan validasi yang standout.
     */
    public function rules(): array
    {
        return [
            // Tanda Vital (Harus angka)
            'height'           => ['required', 'numeric'],
            'weight'           => ['required', 'numeric'],
            'systole'          => ['required', 'integer'],
            'diastole'         => ['required', 'integer'],
            'heart_rate'       => ['required', 'integer'],
            'respiration_rate' => ['required', 'integer'],
            'temperature'      => ['required', 'numeric'],

            // List File yang akan dihapus
            'delete_files' => 'nullable|array',
            'delete_files.*' => 'exists:patient_files,id',

            // Hasil Pemeriksaan (Tulisan Bebas)
            'clinical_notes'   => ['required', 'string'],

            // Berkas Luar (Opsional)
            'patient_files'    => ['nullable', 'array'],
            'patient_files.*'  => ['file', 'mimes:pdf,jpg,png,jpeg', 'max:5120'],

            // Resep Obat (Wajib)
            'medicines'        => ['required', 'array', 'min:1'],
            'medicines.*'      => ['required', 'string'], // ID Obat dari API
            'quantities'       => ['required', 'array', 'min:1'],
            'quantities.*'     => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Custom pesan error dalam bahasa Indonesia yang natural.
     */
    public function messages(): array
    {
        return [
            'required'             => ':attribute wajib diisi.',
            'numeric'              => ':attribute harus berupa angka.',
            'integer'              => ':attribute harus berupa angka bulat.',
            'medicines.*.required' => 'Pilih obat yang ingin diresepkan.',
            'quantities.*.min'     => 'Jumlah obat minimal adalah 1.',
        ];
    }

    /**
     * Custom Nama Atribut agar pesan error lebih enak dibaca.
     */
    public function attributes(): array
    {
        return [
            'clinical_notes' => 'Hasil pemeriksaan',
            'patient_files'  => 'Berkas luar',
            'height'         => 'Tinggi badan',
            'weight'         => 'Berat badan',
        ];
    }
}
