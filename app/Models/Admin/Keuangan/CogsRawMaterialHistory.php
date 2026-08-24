<?php

namespace App\Models\Admin\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Outlet;

class CogsRawMaterialHistory extends Model
{
    use HasFactory;

    protected $table = 'cogs_raw_material_histories';
    protected $primaryKey = 'cogs_raw_material_history_id';

    protected $fillable = [
        'cogs_raw_material_id',
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

    public function rawMaterial()
    {
        return $this->belongsTo(CogsRawMaterial::class, 'cogs_raw_material_id', 'cogs_raw_material_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }
}
