<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServiceChargeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_name' => 'required|string|max:100',
            'rate_percent' => 'required|numeric|min:0|max:100',
            'is_taxable' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'service_name.required' => 'Nama service charge wajib diisi.',
            'service_name.string' => 'Nama service charge harus berupa teks.',
            'service_name.max' => 'Nama service charge maksimal 100 karakter.',
            'rate_percent.required' => 'Tarif persentase service charge wajib diisi.',
            'rate_percent.numeric' => 'Tarif persentase service charge harus berupa angka.',
            'rate_percent.min' => 'Tarif persentase service charge minimal 0%.',
            'rate_percent.max' => 'Tarif persentase service charge maksimal 100%.',
        ];
    }
}
