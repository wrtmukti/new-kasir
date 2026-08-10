<?php

namespace App\Http\Requests\Admin\Keuangan;

use Illuminate\Foundation\Http\FormRequest;

class CogsWasteLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cogs_raw_material_id' => 'required|exists:cogs_raw_materials,cogs_raw_material_id',
            'qty_lost' => 'required|numeric|gt:0',
            'reason' => 'required|string|max:100',
            'loss_date' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'cogs_raw_material_id.required' => 'Bahan mentah terbuang wajib dipilih.',
            'qty_lost.required' => 'Jumlah bahan terbuang wajib diisi.',
            'qty_lost.gt' => 'Jumlah bahan terbuang harus lebih besar dari 0.',
            'reason.required' => 'Alasan bahan terbuang (Basi/Rusak/Tumpah) wajib dipilih/diisi.',
            'loss_date.required' => 'Tanggal kejadian terbuang wajib diisi.',
        ];
    }
}
