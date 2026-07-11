<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Company extends Model
{
    use HasUlids;

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
