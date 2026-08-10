<?php

namespace App\Models\Admin\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CogsRecipeItem extends Model
{
    use HasFactory;

    protected $table = 'cogs_recipe_items';
    protected $primaryKey = 'cogs_recipe_item_id';

    protected $fillable = [
        'cogs_recipe_id',
        'cogs_raw_material_id',
        'ingredient_qty',
        'ingredient_cost',
    ];

    public function recipe()
    {
        return $this->belongsTo(CogsRecipe::class, 'cogs_recipe_id', 'cogs_recipe_id');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(CogsRawMaterial::class, 'cogs_raw_material_id', 'cogs_raw_material_id');
    }
}
