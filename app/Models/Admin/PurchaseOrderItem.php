<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $table = 'purchase_order_items';
    protected $primaryKey = 'po_item_id';

    protected $fillable = [
        'po_id', 'stock_id', 'qty', 'price', 'subtotal',
        'received_qty', 'notes',
        'created_by', 'updated_by', 'delete_status',
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'received_qty' => 'integer',
        'delete_status' => 'integer',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'po_id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'stock_id');
    }
}
