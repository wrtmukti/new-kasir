<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;

class Supplier extends Model
{
    protected $table = 'suppliers';

    protected $primaryKey = 'supplier_id';

    protected $fillable = [
        'company_id',
        'supplier_code',
        'supplier_name',
        'supplier_contact',
        'supplier_phone',
        'supplier_address',
        'supplier_status',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    protected $casts = [
        'supplier_status' => 'integer',
        'delete_status' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}
