<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;

class Order extends Model
{
    protected $table = 'orders';

    protected $primaryKey = 'order_id';

    protected $fillable = [
        'company_id',
        'order_type',
        'order_status',
        'order_grand_total',
        'order_remark',
        'order_transaction_id',
        'order_table_id',
        'order_customer_id',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product', 'order_id', 'product_id')
            ->withPivot('note', 'quantity')
            ->wherePivot('delete_status', 0)
            ->withTimestamps();
    }
}
