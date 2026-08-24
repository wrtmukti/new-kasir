<?php

namespace App\Models\Admin\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Outlet;

class CogsRecipeHistory extends Model
{
    use HasFactory;

    protected $table = 'cogs_recipe_histories';
    protected $primaryKey = 'cogs_recipe_history_id';

    protected $fillable = [
        'cogs_recipe_id',
        'outlet_id',
        'recipe_name',
        'target_food_cost',
        'estimated_cogs',
        'suggested_price',
        'snapshot_items_json',
        'action_type',
        'changed_by',
        'effective_date',
        'history_remark',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    protected $casts = [
        'snapshot_items_json' => 'array',
    ];

    public function recipe()
    {
        return $this->belongsTo(CogsRecipe::class, 'cogs_recipe_id', 'cogs_recipe_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }
}
