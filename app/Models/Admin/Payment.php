<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'outlet_id',
        'transaction_id',
        'payment_metode',
        'payment_amount',
        'payment_reference',
        'payment_status',
        'payment_grand_total',
        'payment_remark',
        'payment_date',
        'payment_table_id',
        'payment_customer_id',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'transaction_id');
    }
}
