<?php

namespace App\Models\SysAdmin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use App\Models\Admin\Stock;
use App\Models\Admin\Product;
use App\Models\Admin\Category;

class Company extends Model
{
    use HasUlids;

    protected $table = 'companies';

    protected $primaryKey = 'company_id';

    protected $fillable = [
        'company_name',
        'company_code',
        'company_branch',
        'company_slug',
        'company_email',
        'company_phone',
        'company_address',
        'company_image',
        'company_status',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    protected $casts = [
        'company_status' => 'integer',
        'delete_status' => 'integer',
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'company_id', 'company_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'company_id', 'company_id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'company_id', 'company_id');
    }
}
