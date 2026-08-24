<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ShiftSetting extends Model
{
    protected $table = 'shift_settings';

    protected $fillable = [
        'outlet_id',
        'daily_cutoff_time',
        'shift_mode',
        'auto_lock_unclosed',
    ];

    protected $casts = [
        'auto_lock_unclosed' => 'integer',
    ];
}
