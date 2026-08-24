<?php

namespace App\Http\Requests\SysAdmin\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'client_name' => 'required|string|max:150',
            'client_code' => 'required|string|max:50|regex:/^[A-Za-z0-9_]+$/',
            'environment' => 'nullable|string|in:dev,prod,staging,test',
            'business_name' => 'nullable|string|max:150',
            'owner_name' => 'required|string|max:100',
            'owner_email' => 'required|email|max:150',
            'owner_phone' => 'nullable|string|max:30',
            'owner_password' => 'required|string|min:6',
            'plan_id' => 'required|exists:plans,id',
            'address' => 'nullable|string|max:500',
            'db_host' => 'nullable|string|max:100',
            'db_port' => 'nullable|integer',
            'db_username' => 'nullable|string|max:100',
            'db_password' => 'nullable|string|max:255',
        ];
    }

    /**
     * Custom Indonesian validation messages as per rule_ai.md
     */
    public function messages(): array
    {
        return [
            'client_name.required' => 'Nama Klien / Perusahaan wajib diisi.',
            'client_name.max' => 'Nama Klien maksimal :max karakter.',
            'client_code.required' => 'Kode Klien / Inisial Database wajib diisi.',
            'client_code.regex' => 'Kode Klien hanya boleh alfanumerik dan underscore (tanpa spasi).',
            'owner_name.required' => 'Nama PIC / Pemilik Usaha wajib diisi.',
            'owner_email.required' => 'Email PIC / Pemilik Usaha wajib diisi.',
            'owner_email.email' => 'Format email pemilik tidak valid.',
            'owner_password.required' => 'Kata sandi akun owner wajib diisi.',
            'owner_password.min' => 'Kata sandi minimal :min karakter.',
            'plan_id.required' => 'Silakan pilih paket langganan SaaS.',
            'plan_id.exists' => 'Paket langganan yang dipilih tidak valid.',
        ];
    }
}
