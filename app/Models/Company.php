<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;


class Company extends Model
{
    use HasUlids;

    protected $fillable = [
        'company_name',
        'company_code',
        'company_slug',
        'company_email',
        'company_phone',
        'company_address',
        'category_image',
        'company_status',
        'created_by',
        'updated_by',
    ];
}
