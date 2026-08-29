<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Keuangan\RawStockMaterial;
use App\Models\Admin\Keuangan\CogsRawMaterial;

class PurchaseOrderItem extends Model
{
    protected $table = 'purchase_order_items';
    protected $primaryKey = 'po_item_id';

    protected $fillable = [
        'po_id', 'raw_stock_material_id', 'cogs_raw_material_id', 'qty', 'price', 'subtotal',
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

    public function rawStockMaterial()
    {
        return $this->belongsTo(RawStockMaterial::class, 'raw_stock_material_id', 'raw_stock_material_id');
    }

    public function cogsRawMaterial()
    {
        return $this->belongsTo(RawStockMaterial::class, 'raw_stock_material_id', 'raw_stock_material_id');
    }
}
