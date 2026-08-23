<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TaxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tax_name' => 'required|string|max:100',
            'rate_percent' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:inclusive,exclusive',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'tax_name.required' => 'Nama pajak wajib diisi.',
            'tax_name.string' => 'Nama pajak harus berupa teks.',
            'tax_name.max' => 'Nama pajak maksimal 100 karakter.',
            'rate_percent.required' => 'Tarif persentase pajak wajib diisi.',
            'rate_percent.numeric' => 'Tarif persentase pajak harus berupa angka.',
            'rate_percent.min' => 'Tarif persentase pajak minimal 0%.',
            'rate_percent.max' => 'Tarif persentase pajak maksimal 100%.',
            'type.required' => 'Tipe pengenaan pajak wajib dipilih.',
            'type.in' => 'Tipe pajak harus berupa Inklusif atau Eksklusif.',
        ];
    }
}
