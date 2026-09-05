<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Outlet;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';
    protected $primaryKey = 'po_id';

    protected $fillable = [
        'outlet_id',
        'po_code',
        'po_date',
        'supplier_id',
        'po_status',
        'payment_status',
        'payment_date',
        'payment_method',
        'due_date',
        'po_total_amount',
        'po_notes',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    protected $casts = [
        'po_date' => 'datetime',
        'payment_date' => 'datetime',
        'due_date' => 'date',
        'po_total_amount' => 'decimal:2',
        'delete_status' => 'integer',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'po_id', 'po_id');
    }

    public function receivings()
    {
        return $this->hasMany(PurchaseReceiving::class, 'po_id', 'po_id');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function getPoNumberAttribute()
    {
        return $this->po_code;
    }
}
