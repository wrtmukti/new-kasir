<?php

namespace App\Models\Admin;

use App\Models\Admin\Outlet;
use Illuminate\Database\Eloquent\Model;

class Bundle extends Model
{
    protected $table = 'bundles';
    protected $primaryKey = 'bundle_id';

    protected $fillable = [
        'outlet_id',
        'bundle_code',
        'bundle_name',
        'bundle_slug',
        'bundle_description',
        'bundle_price',
        'bundle_status',
        'bundle_image',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    protected $casts = [
        'bundle_price' => 'decimal:2',
        'bundle_status' => 'integer',
        'delete_status' => 'integer',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }

    public function items()
    {
        return $this->hasMany(BundleItem::class, 'bundle_id', 'bundle_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'bundle_items', 'bundle_id', 'product_id')
            ->withPivot('quantity', 'price_snapshot')
            ->wherePivot('delete_status', 0)
            ->withTimestamps();
    }
}
