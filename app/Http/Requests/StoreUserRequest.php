<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Pastikan ini true agar request diizinkan
    }

    public function rules(): array
    {
        // Validasi Dasar untuk semua Role (Admin, Doctor, Pharmacist)
        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', Rule::in(['admin', 'doctor', 'pharmacist'])],
            'phone'    => ['required', 'regex:/^08[0-9]{8,11}$/'], // Format 08xxxxx sesuai guideline
        ];

        // Validasi untuk doctor
        if ($this->input('role') === 'doctor') {
            $rules['str_number']     = ['required', 'string', 'unique:doctor_profiles,str_number'];
            $rules['specialization'] = ['required', 'string'];
            $rules['signature']      = ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048']; // Maks 2MB
        }

        // Validasi untuk pharmacist
        if ($this->input('role') === 'pharmacist') {
            $rules['sipa_number'] = ['required', 'string', 'unique:pharmacist_profiles,sipa_number'];
            $rules['work_unit']  = ['required', 'string'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            // Validasi Dasar (Admin, Doctor, Pharmacist)
            'name.required'           => 'Nama wajib diisi.',
            'name.string'             => 'Nama harus berupa teks.',
            'name.max'                => 'Nama maksimal 255 karakter.',
            'email.required'          => 'Email wajib diisi.',
            'email.email'             => 'Format email tidak valid.',
            'email.unique'            => 'Email sudah terdaftar di sistem.',
            'password.required'       => 'Password wajib diisi.',
            'password.min'            => 'Password minimal harus :min karakter.',
            'password.confirmed'      => 'Konfirmasi password tidak cocok.',
            'role.required'           => 'Role wajib dipilih.',
            'role.in'                 => 'Role harus dipilih antara Admin, Doctor, atau Pharmacist (Apoteker).',
            'phone.required'          => 'Nomor HP wajib diisi.',
            'phone.regex'             => 'Format nomor HP harus dimulai dengan 08 dan berjumlah 10-13 digit.',

            // Untuk doctor
            'str_number.required'     => 'Dokter wajib mencantumkan Nomor STR.',
            'str_number.unique'       => 'Nomor STR sudah terdaftar.',
            'specialization.required' => 'Spesialisasi wajib diisi.',
            'signature.required'      => 'Tanda tangan digital wajib diunggah untuk Dokter.',
            'signature.image'         => 'File tanda tangan harus berupa gambar.',
            'signature.mimes'         => 'Format gambar tanda tangan harus PNG, JPG, atau JPEG.',
            'signature.max'           => 'Ukuran gambar tanda tangan maksimal 2MB.',

            // Untuk pharmacist
            'sipa_number.required'    => 'Apoteker wajib mencantumkan Nomor SIPA.',
            'sipa_number.unique'      => 'Nomor SIPA sudah terdaftar.',
            'work_unit.required'     => 'Unit kerja apoteker wajib diisi.',
        ];
    }
}
