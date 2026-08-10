<?php

namespace App\Http\Requests\Admin\Keuangan;

use Illuminate\Foundation\Http\FormRequest;

class CogsRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipe_name' => 'required|string|max:255',
            'product_id' => 'nullable|exists:products,product_id',
            'target_food_cost' => 'required|numeric|min:0.01|max:100',
            'items' => 'required|array|min:1',
            'items.*.cogs_raw_material_id' => 'required|exists:cogs_raw_materials,cogs_raw_material_id',
            'items.*.ingredient_qty' => 'required|numeric|gt:0',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'recipe_name.required' => 'Nama resep standar wajib diisi.',
            'target_food_cost.required' => 'Target Food Cost % wajib diisi.',
            'target_food_cost.numeric' => 'Target Food Cost harus berupa angka.',
            'target_food_cost.min' => 'Target Food Cost minimal 0.01%.',
            'target_food_cost.max' => 'Target Food Cost maksimal 100%.',
            'items.required' => 'Minimal harus menambahkan 1 bahan penyusun resep.',
            'items.min' => 'Minimal harus menambahkan 1 bahan penyusun resep.',
            'items.*.cogs_raw_material_id.required' => 'Bahan mentah wajib dipilih.',
            'items.*.ingredient_qty.required' => 'Takaran bahan wajib diisi.',
            'items.*.ingredient_qty.gt' => 'Takaran bahan harus lebih besar dari 0.',
        ];
    }
}
