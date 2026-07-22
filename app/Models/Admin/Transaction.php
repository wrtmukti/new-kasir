<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'company_id',
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

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'transaction_id', 'order_transaction_id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class, 'transaction_id', 'transaction_id');
    }
}
