<?php

namespace App\Http\Requests\SysAdmin;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'company_code' => 'nullable|string|max:50',
            'company_branch' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_address' => 'nullable|string',
            'company_status' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Nama perusahaan wajib diisi.',
            'company_name.max' => 'Nama perusahaan maksimal :max karakter.',
            'company_code.max' => 'Kode perusahaan maksimal :max karakter.',
            'company_branch.max' => 'Cabang maksimal :max karakter.',
            'company_email.email' => 'Format email tidak valid.',
            'company_email.max' => 'Email maksimal :max karakter.',
            'company_phone.max' => 'Telepon maksimal :max karakter.',
            'company_status.in' => 'Status tidak valid.',
        ];
    }
}
