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
        'raw_stock_material_id',
        'ingredient_qty',
        'ingredient_cost',
    ];

    public function recipe()
    {
        return $this->belongsTo(CogsRecipe::class, 'cogs_recipe_id', 'cogs_recipe_id');
    }

    public function rawStockMaterial()
    {
        return $this->belongsTo(RawStockMaterial::class, 'raw_stock_material_id', 'raw_stock_material_id');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawStockMaterial::class, 'raw_stock_material_id', 'raw_stock_material_id');
    }
}
