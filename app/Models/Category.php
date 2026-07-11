<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'category_id';

    protected $fillable = [
        'company_id',
        'category_name',
        'category_slug',
        'category_description',
        'category_type',
        'category_status',
        'category_image',
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
