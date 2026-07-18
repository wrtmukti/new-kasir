<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'nullable|string|max:255',
            'stock_code' => 'nullable|string|max:50',
            'stock_name' => 'required|string|max:255',
            'stock_description' => 'nullable|string',
            'stock_type' => 'nullable|string|max:50',
            'stock_unit' => 'nullable|string|max:20',
            'stock_amount' => 'nullable|integer|min:0',
            'stock_price' => 'nullable|numeric|min:0',
            'stock_status' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'stock_name.required' => 'Nama stok wajib diisi.',
            'stock_name.max' => 'Nama stok maksimal :max karakter.',
            'stock_code.max' => 'Kode stok maksimal :max karakter.',
            'stock_type.max' => 'Tipe maksimal :max karakter.',
            'stock_unit.max' => 'Unit maksimal :max karakter.',
            'stock_amount.integer' => 'Jumlah stok harus angka.',
            'stock_amount.min' => 'Jumlah stok minimal 0.',
            'stock_price.numeric' => 'Harga harus angka.',
            'stock_price.min' => 'Harga minimal 0.',
            'stock_status.in' => 'Status tidak valid.',
        ];
    }
}
