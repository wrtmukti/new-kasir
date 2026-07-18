<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'nullable|string|max:255',
            'supplier_code' => 'nullable|string|max:50',
            'supplier_name' => 'required|string|max:255',
            'supplier_contact' => 'nullable|string|max:255',
            'supplier_phone' => 'nullable|string|max:50',
            'supplier_address' => 'nullable|string',
            'supplier_status' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_name.required' => 'Nama supplier wajib diisi.',
            'supplier_name.max' => 'Nama supplier maksimal :max karakter.',
            'supplier_code.max' => 'Kode supplier maksimal :max karakter.',
            'supplier_contact.max' => 'Kontak maksimal :max karakter.',
            'supplier_phone.max' => 'Telepon maksimal :max karakter.',
            'supplier_status.in' => 'Status tidak valid.',
        ];
    }
}
