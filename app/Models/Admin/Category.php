<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;

class Category extends Model
{
    protected $table = 'categories';

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
