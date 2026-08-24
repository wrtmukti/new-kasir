<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Outlet;

class Tax extends Model
{
    protected $table = 'taxes';
    protected $primaryKey = 'tax_id';

    protected $fillable = [
        'outlet_id',
        'tax_name',
        'rate_percent',
        'type',
        'is_active',
        'delete_status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'rate_percent' => 'float',
        'is_active' => 'integer',
        'delete_status' => 'integer',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }
}
