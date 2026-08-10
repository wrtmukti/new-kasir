<?php

namespace App\Models\Admin\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;

class CogsWasteHistory extends Model
{
    use HasFactory;

    protected $table = 'cogs_waste_histories';
    protected $primaryKey = 'cogs_waste_history_id';

    protected $fillable = [
        'cogs_waste_log_id',
        'company_id',
        'cogs_raw_material_id',
        'qty_lost',
        'waste_cost',
        'reason',
        'loss_date',
        'action_type',
        'changed_by',
        'history_remark',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    public function wasteLog()
    {
        return $this->belongsTo(CogsWasteLog::class, 'cogs_waste_log_id', 'cogs_waste_log_id');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(CogsRawMaterial::class, 'cogs_raw_material_id', 'cogs_raw_material_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}
