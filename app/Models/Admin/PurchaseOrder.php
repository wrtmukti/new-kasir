<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';
    protected $primaryKey = 'po_id';

    protected $fillable = [
        'company_id', 'po_code', 'po_date', 'supplier_id',
        'po_status', 'po_total_amount', 'po_notes',
        'created_by', 'updated_by', 'delete_status',
    ];

    protected $casts = [
        'po_date' => 'datetime',
        'po_total_amount' => 'decimal:2',
        'delete_status' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
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
}
