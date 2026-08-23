<?php

namespace App\Models\Admin;

use App\Models\SysAdmin\Company;
use Illuminate\Database\Eloquent\Model;

class SettingOutlet extends Model
{
    protected $table = 'setting_outlets';

    protected $fillable = [
        'company_id',
        'outlet_name',
        'payment_timing',
        'theme',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    protected $casts = [
        'delete_status' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}
