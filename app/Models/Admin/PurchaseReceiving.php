<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;

class PurchaseReceiving extends Model
{
    protected $table = 'purchase_receivings';
    protected $primaryKey = 'receiving_id';

    protected $fillable = [
        'company_id', 'receiving_code', 'receiving_date',
        'po_id', 'po_code', 'receiving_status', 'receiving_notes',
        'received_by', 'created_by', 'updated_by', 'delete_status',
    ];

    protected $casts = [
        'receiving_date' => 'datetime',
        'delete_status' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
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
