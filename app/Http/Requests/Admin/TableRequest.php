<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'nullable|string|max:255',
            'table_number' => 'required|integer|min:1',
            'table_status' => 'nullable|string|in:inactive,active,reserved,occupied',
            'table_capacity' => 'nullable|integer|min:1',
            'table_description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'table_number.required' => 'Nomor meja wajib diisi.',
            'table_number.integer' => 'Nomor meja harus angka.',
            'table_number.min' => 'Nomor meja minimal 1.',
            'table_status.in' => 'Status meja tidak valid.',
            'table_capacity.integer' => 'Kapasitas harus angka.',
            'table_capacity.min' => 'Kapasitas minimal 1.',
        ];
    }
}
