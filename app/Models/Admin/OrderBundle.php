<?php

namespace App\Models\Admin;

use App\Models\Admin\Outlet;
use Illuminate\Database\Eloquent\Model;

class OrderBundle extends Model
{
    protected $table = 'order_bundle';

    protected $fillable = [
        'outlet_id',
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

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
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
