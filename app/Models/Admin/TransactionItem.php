<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $table = 'transaction_items';

    protected $primaryKey = 'item_id';

    protected $fillable = [
        'company_id',
        'transaction_id',
        'product_id',
        'product_name',
        'price',
        'discount_type',
        'discount_value',
        'discount_amount',
        'qty',
        'subtotal',
        'note',
        'created_by',
        'delete_status',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'transaction_id');
    }
}
