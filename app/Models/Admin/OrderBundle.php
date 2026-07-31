<?php

namespace App\Models\Admin;

use App\Models\SysAdmin\Company;
use Illuminate\Database\Eloquent\Model;

class OrderBundle extends Model
{
    protected $table = 'order_bundle';

    protected $fillable = [
        'company_id',
        'order_id',
        'transaction_id',
        'bundle_id',
        'bundle_name',
        'bundle_price',
        'quantity',
        'subtotal',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    protected $casts = [
        'bundle_price' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
        'delete_status' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function bundle()
    {
        return $this->belongsTo(Bundle::class, 'bundle_id', 'bundle_id');
    }
}
