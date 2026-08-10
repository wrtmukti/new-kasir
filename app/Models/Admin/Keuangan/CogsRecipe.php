<?php

namespace App\Models\Admin\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Product;
use App\Models\SysAdmin\Company;

class CogsRecipe extends Model
{
    use HasFactory;

    protected $table = 'cogs_recipes';
    protected $primaryKey = 'cogs_recipe_id';

    protected $fillable = [
        'company_id',
        'product_id',
        'recipe_name',
        'recipe_category',
        'target_food_cost',
        'estimated_cogs',
        'suggested_price',
        'notes',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function items()
    {
        return $this->hasMany(CogsRecipeItem::class, 'cogs_recipe_id', 'cogs_recipe_id');
    }

    public function histories()
    {
        return $this->hasMany(CogsRecipeHistory::class, 'cogs_recipe_id', 'cogs_recipe_id');
    }

    // Recalculate HPP & suggested price
    public function recalculateCost()
    {
        $totalCost = 0;
        $this->load('items.rawMaterial');
        foreach ($this->items as $item) {
            if ($item->rawMaterial) {
                $itemCost = (float) $item->ingredient_qty * (float) $item->rawMaterial->effective_price;
                $item->update(['ingredient_cost' => $itemCost]);
                $totalCost += $itemCost;
            }
        }
        $targetFc = max(0.01, (float) $this->target_food_cost);
        $suggestedPrice = $totalCost / ($targetFc / 100);

        $this->update([
            'estimated_cogs' => $totalCost,
            'suggested_price' => $suggestedPrice,
        ]);

        return $totalCost;
    }
}
