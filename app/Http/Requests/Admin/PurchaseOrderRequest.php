<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'supplier_id' => 'nullable|string|max:255',
            'po_notes' => 'nullable|string',
            'po_status' => 'nullable|string|in:draft,ordered',
            'items' => 'required|array|min:1',
            'items.*.stock_id' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'items.required' => 'Minimal 1 item bahan.',
            'items.min' => 'Minimal 1 item bahan.',
            'items.*.stock_id.required' => 'Bahan wajib dipilih.',
            'items.*.qty.required' => 'Jumlah wajib diisi.',
            'items.*.qty.min' => 'Jumlah minimal 1.',
            'items.*.price.required' => 'Harga wajib diisi.',
            'items.*.price.min' => 'Harga minimal 0.',
        ];
    }
}
