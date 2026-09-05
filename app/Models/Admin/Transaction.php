<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Outlet;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'outlet_id',
        'daily_closing_id',
        'transaction_code',
        'transaction_date',
        'transaction_tax',
        'transaction_subtotal',
        'transaction_service_charge',
        'voucher_id',
        'transaction_grand_total',
        'transaction_status',
        'cancel_reason',
        'transaction_remark',
        'payment_id',
        'transaction_table_id',
        'transaction_customer_id',
        'discount_id',
        'discount_name',
        'discount_type',
        'discount_value',
        'discount_amount',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }

    public function dailyClosing()
    {
        return $this->belongsTo(DailyClosing::class, 'daily_closing_id', 'id');
    }


    public function order()
    {
        return $this->belongsTo(Order::class, 'transaction_id', 'order_transaction_id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class, 'transaction_id', 'transaction_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'transaction_id', 'transaction_id');
    }

    public function bundles()
    {
        return $this->hasMany(OrderBundle::class, 'transaction_id', 'transaction_id')
            ->where('delete_status', 0);
    }
}
