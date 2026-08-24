<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'outlet_id' => 'nullable|string|max:255',
            'voucher_code' => 'required|string|max:50|unique:vouchers,voucher_code,' . $this->route('voucher')?->voucher_id . ',voucher_id',
            'voucher_name' => 'required|string|max:255',
            'voucher_type' => 'required|string|in:nominal,percentage,free_item',
            'voucher_value' => 'required|numeric|min:0',
            'voucher_max_discount' => 'nullable|numeric|min:0',
            'voucher_min_purchase' => 'nullable|numeric|min:0',
            'voucher_applicable_to' => 'nullable|string|in:all,specific_products,specific_categories',
            'voucher_usage_limit' => 'nullable|integer|min:0',
            'voucher_usage_per_customer' => 'nullable|integer|min:0',
            'voucher_start_date' => 'nullable|date',
            'voucher_end_date' => 'nullable|date|after_or_equal:voucher_start_date',
            'voucher_status' => 'nullable|integer|in:0,1',
            'voucher_description' => 'nullable|string',
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'voucher_code.required' => 'Kode voucher wajib diisi.',
            'voucher_code.max' => 'Kode voucher maksimal :max karakter.',
            'voucher_code.unique' => 'Kode voucher sudah digunakan.',
            'voucher_name.required' => 'Nama voucher wajib diisi.',
            'voucher_name.max' => 'Nama voucher maksimal :max karakter.',
            'voucher_type.required' => 'Tipe voucher wajib dipilih.',
            'voucher_type.in' => 'Tipe voucher harus nominal, percentage, atau free_item.',
            'voucher_value.required' => 'Nilai voucher wajib diisi.',
            'voucher_value.numeric' => 'Nilai voucher harus berupa angka.',
            'voucher_value.min' => 'Nilai voucher tidak boleh negatif.',
            'voucher_max_discount.numeric' => 'Maksimal diskon harus berupa angka.',
            'voucher_max_discount.min' => 'Maksimal diskon tidak boleh negatif.',
            'voucher_min_purchase.numeric' => 'Minimal pembelian harus berupa angka.',
            'voucher_min_purchase.min' => 'Minimal pembelian tidak boleh negatif.',
            'voucher_applicable_to.in' => 'Penerapan voucher tidak valid.',
            'voucher_usage_limit.integer' => 'Batas penggunaan harus berupa angka.',
            'voucher_usage_limit.min' => 'Batas penggunaan tidak boleh negatif.',
            'voucher_usage_per_customer.integer' => 'Batas per pelanggan harus berupa angka.',
            'voucher_usage_per_customer.min' => 'Batas per pelanggan tidak boleh negatif.',
            'voucher_start_date.date' => 'Tanggal mulai tidak valid.',
            'voucher_end_date.date' => 'Tanggal berakhir tidak valid.',
            'voucher_end_date.after_or_equal' => 'Tanggal berakhir harus setelah atau sama dengan tanggal mulai.',
            'voucher_status.in' => 'Status tidak valid.',
        ];
    }
}
