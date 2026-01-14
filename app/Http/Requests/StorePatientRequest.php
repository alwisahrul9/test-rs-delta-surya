<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientRequest extends FormRequest
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
            // Tanda-tanda Vital (Wajib)
            'name'             => ['required'],
            'born_date'        => ['required', 'date'],
            'sex'              => ['required', Rule::in(['m', 'f'])],
            'height'           => ['required', 'numeric'],
            'weight'           => ['required', 'numeric'],
            'systole'          => ['required', 'integer'],
            'diastole'         => ['required', 'integer'],
            'heart_rate'       => ['required', 'integer'],
            'respiration_rate' => ['required', 'integer'],
            'temperature'      => ['required', 'numeric'],

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

    public function messages(): array
    {
        return [
            'required'             => ':attribute wajib diisi.',
            'numeric'              => ':attribute harus berupa angka.',
            'integer'              => ':attribute harus berupa angka bulat.',
            'mimes'                => 'Format berkas harus berupa PDF, JPG, atau PNG.',
            'medicines.*.required' => 'Pilih obat yang ingin diresepkan.',
            'quantities.*.min'     => 'Jumlah obat minimal adalah 1.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'             => 'Nama',
            'born_date'        => 'Tanggal Lahir',
            'sex'              => 'Jenis Kelamin',
            'clinical_notes'   => 'Hasil pemeriksaan',
            'patient_files'    => 'Berkas luar',
            'height'           => 'Tinggi badan',
            'weight'           => 'Berat badan',
            'systole'          => 'Systole',
            'systole'          => 'Systole',
            'diastole'         => 'Diastole',
            'heart_rate'       => 'Heart Rate',
            'temperature'      => 'Temperature',
            'respiration_rate' => 'Respiration Rate',
        ];
    }
}
