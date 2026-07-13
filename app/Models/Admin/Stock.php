<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;

class Stock extends Model
{
    protected $table = 'stocks';

    protected $primaryKey = 'stock_id';

    protected $fillable = [
        'company_id',
        'stock_code',
        'stock_name',
        'stock_slug',
        'stock_description',
        'stock_type',
        'stock_unit',
        'stock_counted',
        'stock_amount',
        'stock_price',
        'stock_status',
        'stock_image',
        'stock_remark',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    protected $casts = [
        'stock_counted' => 'integer',
        'stock_amount' => 'integer',
        'stock_price' => 'decimal:2',
        'stock_status' => 'integer',
        'delete_status' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}
