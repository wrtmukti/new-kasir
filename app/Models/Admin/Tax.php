<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;

class Tax extends Model
{
    protected $table = 'taxes';
    protected $primaryKey = 'tax_id';

    protected $fillable = [
        'company_id',
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

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}
