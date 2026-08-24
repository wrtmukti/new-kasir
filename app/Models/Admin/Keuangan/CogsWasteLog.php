<?php

namespace App\Models\Admin\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Outlet;

class CogsWasteLog extends Model
{
    use HasFactory;

    protected $table = 'cogs_waste_logs';
    protected $primaryKey = 'cogs_waste_log_id';

    protected $fillable = [
        'outlet_id',
        'cogs_raw_material_id',
        'qty_lost',
        'waste_cost',
        'reason',
        'loss_date',
        'notes',
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

    public function histories()
    {
        return $this->hasMany(CogsWasteHistory::class, 'cogs_waste_log_id', 'cogs_waste_log_id');
    }
}
