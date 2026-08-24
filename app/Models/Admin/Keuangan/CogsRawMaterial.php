<?php

namespace App\Models\Admin\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Outlet;

class CogsRawMaterial extends Model
{
    use HasFactory;

    protected $table = 'cogs_raw_materials';
    protected $primaryKey = 'cogs_raw_material_id';

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
        return $this->hasMany(CogsRawMaterialHistory::class, 'cogs_raw_material_id', 'cogs_raw_material_id');
    }

    public function wasteLogs()
    {
        return $this->hasMany(CogsWasteLog::class, 'cogs_raw_material_id', 'cogs_raw_material_id');
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(\App\Models\Admin\PurchaseOrderItem::class, 'cogs_raw_material_id', 'cogs_raw_material_id');
    }

    public function purchaseReceivingItems()
    {
        return $this->hasMany(\App\Models\Admin\PurchaseReceivingItem::class, 'cogs_raw_material_id', 'cogs_raw_material_id');
    }

    // Helper calculate effective_price
    public function calculatePrices()
    {
        $yield = max(0.01, 100 - (float) $this->loss_percent);
        $this->yield_percent = $yield;
        $this->effective_price = (float) $this->price_per_unit / ($yield / 100);
        return $this->effective_price;
    }
}
