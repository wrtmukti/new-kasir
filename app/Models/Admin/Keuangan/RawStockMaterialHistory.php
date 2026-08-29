<?php

namespace App\Models\Admin\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Outlet;

class RawStockMaterialHistory extends Model
{
    use HasFactory;

    protected $table = 'raw_stock_material_histories';
    protected $primaryKey = 'raw_stock_material_history_id';

    protected $fillable = [
        'raw_stock_material_id',
        'outlet_id',
        'name',
        'unit',
        'amount',
        'price_per_unit',
        'loss_percent',
        'yield_percent',
        'effective_price',
        'action_type',
        'changed_by',
        'effective_date',
        'history_remark',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    public function rawStockMaterial()
    {
        return $this->belongsTo(RawStockMaterial::class, 'raw_stock_material_id', 'raw_stock_material_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }
}
