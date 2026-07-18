<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseReceivingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'receiving_date' => 'required|date',
            'receiving_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.po_item_id' => 'required|string',
            'items.*.stock_id' => 'required|string',
            'items.*.received_qty' => 'required|integer|min:0',
            'items.*.received_price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'receiving_date.required' => 'Tanggal penerimaan wajib diisi.',
            'items.required' => 'Minimal 1 item diterima.',
            'items.*.received_qty.min' => 'Jumlah diterima minimal 0.',
            'items.*.received_price.min' => 'Harga minimal 0.',
        ];
    }
}
