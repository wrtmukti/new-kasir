<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;

class ServiceCharge extends Model
{
    protected $table = 'service_charges';
    protected $primaryKey = 'service_charge_id';

    protected $fillable = [
        'company_id',
        'service_name',
        'rate_percent',
        'is_taxable',
        'is_active',
        'delete_status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'rate_percent' => 'float',
        'is_taxable' => 'integer',
        'is_active' => 'integer',
        'delete_status' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}
