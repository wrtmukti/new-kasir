<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'company_id' => 'nullable|string|max:255',
            'discount_name' => 'required|string|max:255',
            'discount_type' => 'required|string|in:percentage,nominal',
            'discount_value' => 'required|numeric|min:0',
            'discount_max_amount' => 'nullable|numeric|min:0',
            'discount_description' => 'nullable|string',
            'discount_status' => 'nullable|integer|in:0,1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'discount_name.required' => 'Nama diskon wajib diisi.',
            'discount_name.max' => 'Nama diskon maksimal :max karakter.',
            'discount_type.required' => 'Tipe diskon wajib dipilih.',
            'discount_type.in' => 'Tipe diskon harus percentage atau nominal.',
            'discount_value.required' => 'Nilai diskon wajib diisi.',
            'discount_value.numeric' => 'Nilai diskon harus berupa angka.',
            'discount_value.min' => 'Nilai diskon tidak boleh negatif.',
            'discount_max_amount.numeric' => 'Maksimal amount harus berupa angka.',
            'discount_max_amount.min' => 'Maksimal amount tidak boleh negatif.',
            'discount_status.in' => 'Status tidak valid.',
            'start_date.date' => 'Tanggal mulai tidak valid.',
            'end_date.date' => 'Tanggal berakhir tidak valid.',
            'end_date.after_or_equal' => 'Tanggal berakhir harus setelah atau sama dengan tanggal mulai.',
        ];
    }
}
