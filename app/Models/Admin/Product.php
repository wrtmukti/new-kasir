<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;
use App\Models\Admin\Discount;

class Product extends Model
{
    protected $table = 'products';

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'company_id',
        'category_id',
        'product_code',
        'product_name',
        'product_slug',
        'product_description',
        'product_price',
        'product_status',
        'product_image',
        'category_remark',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'product_stock', 'product_id', 'stock_id')
            ->withPivot('quantity')
            ->wherePivot('delete_status', 0)
            ->withTimestamps();
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_product', 'product_id', 'discount_id')
            ->withPivot('start_date', 'end_date')
            ->wherePivot('delete_status', 0)
            ->withTimestamps();
    }

    public function activeDiscount()
    {
        return $this->belongsToMany(Discount::class, 'discount_product', 'product_id', 'discount_id')
            ->wherePivot('delete_status', 0)
            ->wherePivot('end_date', null)
            ->withTimestamps();
    }
}
