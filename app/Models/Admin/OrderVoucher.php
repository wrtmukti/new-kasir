<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class OrderVoucher extends Model
{
    protected $table = 'order_voucher';

    protected $fillable = [
        'outlet_id',
        'order_id',
        'voucher_code',
        'voucher_type',
        'voucher_value',
        'voucher_max_discount',
        'voucher_amount',
        'created_by',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
