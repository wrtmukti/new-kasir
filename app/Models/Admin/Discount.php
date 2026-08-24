<?php

namespace App\Models\Admin;

use App\Models\Admin\Outlet;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $table = 'discounts';
    protected $primaryKey = 'discount_id';

    protected $fillable = [
        'outlet_id',
        'discount_name',
        'discount_type',
        'discount_value',
        'discount_max_amount',
        'discount_description',
        'discount_status',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'discount_max_amount' => 'decimal:2',
        'discount_status' => 'integer',
        'delete_status' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }

    public function activeProducts()
    {
        return $this->belongsToMany(Product::class, 'discount_product', 'discount_id', 'product_id')
            ->wherePivot('delete_status', 0)
            ->wherePivot('end_date', null)
            ->withTimestamps();
    }
}
