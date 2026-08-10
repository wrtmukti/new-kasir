<?php

namespace App\Models\Admin\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;

class CogsWasteLog extends Model
{
    use HasFactory;

    protected $table = 'cogs_waste_logs';
    protected $primaryKey = 'cogs_waste_log_id';

    protected $fillable = [
        'company_id',
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

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function histories()
    {
        return $this->hasMany(CogsWasteHistory::class, 'cogs_waste_log_id', 'cogs_waste_log_id');
    }
}
