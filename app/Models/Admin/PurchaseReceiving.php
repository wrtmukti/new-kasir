<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Outlet;

class PurchaseReceiving extends Model
{
    protected $table = 'purchase_receivings';
    protected $primaryKey = 'receiving_id';

    protected $fillable = [
        'outlet_id', 'receiving_code', 'receiving_date',
        'po_id', 'po_code', 'receiving_status', 'receiving_notes',
        'received_by', 'created_by', 'updated_by', 'delete_status',
    ];

    protected $casts = [
        'receiving_date' => 'datetime',
        'delete_status' => 'integer',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'po_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseReceivingItem::class, 'receiving_id', 'receiving_id');
    }
}
