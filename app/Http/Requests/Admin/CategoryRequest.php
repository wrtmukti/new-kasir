<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'nullable|string|max:255',
            'category_name' => 'required|string|max:100',
            'category_description' => 'nullable|string',
            'category_type' => 'nullable|string|max:100',
            'category_status' => 'nullable|integer|in:0,1',
            'category_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'category_remark' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'category_name.required' => 'Nama kategori wajib diisi.',
            'category_name.max' => 'Nama kategori maksimal :max karakter.',
            'category_type.max' => 'Tipe maksimal :max karakter.',
            'category_status.in' => 'Status tidak valid.',
            'category_image.image' => 'File harus berupa gambar.',
            'category_image.mimes' => 'Format gambar harus jpeg, png, jpg, webp, atau svg.',
            'category_image.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
