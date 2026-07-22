<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BundleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('product_ids') && is_string($this->input('product_ids'))) {
            $decoded = json_decode($this->input('product_ids'), true);
            $this->merge([
                'product_ids' => is_array($decoded) ? $decoded : [],
            ]);
        }

        if ($this->has('quantities') && is_string($this->input('quantities'))) {
            $decoded = json_decode($this->input('quantities'), true);
            $this->merge([
                'quantities' => is_array($decoded) ? $decoded : [],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'company_id' => 'nullable|string|max:255',
            'bundle_code' => 'nullable|string|max:50',
            'bundle_name' => 'required|string|max:255',
            'bundle_description' => 'nullable|string',
            'bundle_price' => 'nullable|numeric|min:0',
            'bundle_status' => 'nullable|integer|in:0,1',
            'bundle_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'nullable|integer|exists:products,product_id',
            'quantities' => 'nullable|array',
            'quantities.*' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'bundle_name.required' => 'Nama paket wajib diisi.',
            'bundle_name.max' => 'Nama paket maksimal :max karakter.',
            'bundle_price.numeric' => 'Harga paket harus angka.',
            'bundle_price.min' => 'Harga paket minimal 0.',
            'bundle_status.in' => 'Status tidak valid.',
            'bundle_image.image' => 'File harus berupa gambar.',
            'bundle_image.mimes' => 'Format gambar harus jpeg, png, jpg, webp, atau svg.',
            'bundle_image.max' => 'Ukuran gambar maksimal 2MB.',
            'product_ids.*.exists' => 'Produk dengan ID tersebut tidak ditemukan.',
            'quantities.*.integer' => 'Jumlah produk harus angka.',
            'quantities.*.min' => 'Jumlah produk minimal 1.',
        ];
    }
}
