<?php

namespace App\Models\Admin;

use App\Models\Admin\Outlet;
use Illuminate\Database\Eloquent\Model;

class SettingOutlet extends Model
{
    protected $table = 'setting_outlets';

    protected $fillable = [
        'outlet_id',
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

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }
}
