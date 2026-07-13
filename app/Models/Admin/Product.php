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
}
