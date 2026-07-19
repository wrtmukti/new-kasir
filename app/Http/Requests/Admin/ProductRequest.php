<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,category_id',
            'product_code' => 'nullable|string|max:50',
            'product_name' => 'required|string|max:255',
            'product_description' => 'nullable|string',
            'product_price' => 'nullable|numeric|min:0',
            'product_status' => 'nullable|integer|in:0,1',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'category_remark' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'product_name.required' => 'Nama produk wajib diisi.',
            'product_name.max' => 'Nama produk maksimal :max karakter.',
            'product_code.max' => 'Kode produk maksimal :max karakter.',
            'product_price.numeric' => 'Harga harus angka.',
            'product_price.min' => 'Harga minimal 0.',
            'product_status.in' => 'Status tidak valid.',
            'category_id.exists' => 'Kategori tidak ditemukan.',
            'product_image.image' => 'File harus berupa gambar.',
            'product_image.mimes' => 'Format gambar harus jpeg, png, jpg, webp, atau svg.',
            'product_image.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
