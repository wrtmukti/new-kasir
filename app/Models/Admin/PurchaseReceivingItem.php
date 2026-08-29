<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Keuangan\RawStockMaterial;
use App\Models\Admin\Keuangan\CogsRawMaterial;

class PurchaseReceivingItem extends Model
{
    protected $table = 'purchase_receiving_items';
    protected $primaryKey = 'receiving_item_id';

    protected $fillable = [
        'receiving_id', 'po_item_id', 'raw_stock_material_id', 'cogs_raw_material_id',
        'received_qty', 'received_price', 'subtotal', 'notes',
        'created_by', 'updated_by', 'delete_status',
    ];

    protected $casts = [
        'received_qty' => 'integer',
        'received_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'delete_status' => 'integer',
    ];

    public function receiving()
    {
        return $this->belongsTo(PurchaseReceiving::class, 'receiving_id', 'receiving_id');
    }

    public function poItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'po_item_id', 'po_item_id');
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
