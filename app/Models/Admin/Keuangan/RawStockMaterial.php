<?php

namespace App\Models\Admin\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Outlet;

class RawStockMaterial extends Model
{
    use HasFactory;

    protected $table = 'raw_stock_materials';
    protected $primaryKey = 'raw_stock_material_id';

    protected $fillable = [
        'outlet_id',
        'raw_material_code',
        'name',
        'slug',
        'unit',
        'amount',
        'min_amount',
        'price_per_unit',
        'loss_percent',
        'yield_percent',
        'effective_price',
        'notes',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }

    public function histories()
    {
        return $this->hasMany(RawStockMaterialHistory::class, 'raw_stock_material_id', 'raw_stock_material_id');
    }

    public function wasteLogs()
    {
        return $this->hasMany(CogsWasteLog::class, 'raw_stock_material_id', 'raw_stock_material_id');
    }

    public function recipeItems()
    {
        return $this->hasMany(CogsRecipeItem::class, 'raw_stock_material_id', 'raw_stock_material_id');
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(\App\Models\Admin\PurchaseOrderItem::class, 'raw_stock_material_id', 'raw_stock_material_id');
    }

    public function purchaseReceivingItems()
    {
        return $this->hasMany(\App\Models\Admin\PurchaseReceivingItem::class, 'raw_stock_material_id', 'raw_stock_material_id');
    }

    // Helper calculate effective_price
    public function calculatePrices()
    {
        $loss = (float)$this->loss_percent;
        $this->yield_percent = max(0, 100 - $loss);
        if ($this->yield_percent > 0) {
            $this->effective_price = ($this->price_per_unit / $this->yield_percent) * 100;
        } else {
            $this->effective_price = $this->price_per_unit;
        }
    }
}
