<?php

namespace App\Http\Requests\Admin\Keuangan;

use Illuminate\Foundation\Http\FormRequest;

class CogsRawMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:20',
            'amount' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'loss_percent' => 'nullable|numeric|min:0|max:99.99',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama bahan mentah wajib diisi.',
            'name.max' => 'Nama bahan mentah maksimal :max karakter.',
            'unit.required' => 'Satuan unit wajib dipilih/diisi.',
            'amount.required' => 'Jumlah stok fisik wajib diisi.',
            'amount.numeric' => 'Jumlah stok harus berupa angka.',
            'price_per_unit.required' => 'Harga beli per unit wajib diisi.',
            'price_per_unit.numeric' => 'Harga beli harus berupa angka.',
            'loss_percent.numeric' => 'Persentase susut harus berupa angka.',
            'loss_percent.max' => 'Persentase susut maksimal 99.99%.',
        ];
    }
}
