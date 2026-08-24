<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Outlet;

class ServiceCharge extends Model
{
    protected $table = 'service_charges';
    protected $primaryKey = 'service_charge_id';

    protected $fillable = [
        'outlet_id',
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

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }
}
