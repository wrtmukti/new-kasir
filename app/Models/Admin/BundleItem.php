<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class BundleItem extends Model
{
    protected $table = 'bundle_items';
    protected $primaryKey = 'bundle_item_id';

    protected $fillable = [
        'bundle_id',
        'product_id',
        'quantity',
        'price_snapshot',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_snapshot' => 'decimal:2',
        'delete_status' => 'integer',
    ];

    public function bundle()
    {
        return $this->belongsTo(Bundle::class, 'bundle_id', 'bundle_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
