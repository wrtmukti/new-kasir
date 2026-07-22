<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;

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
        'product_discount_id',
        'product_discount_type',
        'product_discount_value',
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
}
