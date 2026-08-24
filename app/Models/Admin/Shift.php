<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\DailyClosing;

class Shift extends Model
{
    protected $table = 'shifts';

    protected $fillable = [
        'outlet_id',
        'shift_number',
        'shift_name',
        'start_time',
        'end_time',
        'default_starting_cash',
        'is_active',
    ];

    protected $casts = [
        'shift_number' => 'integer',
        'default_starting_cash' => 'float',
        'is_active' => 'integer',
    ];

    public function dailyClosings()
    {
        return $this->hasMany(DailyClosing::class, 'shift_number', 'shift_number');
    }
}
