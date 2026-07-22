<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'order_product';

    protected $fillable = [
        'company_id',
        'product_id',
        'order_id',
        'note',
        'quantity',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
