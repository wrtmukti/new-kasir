<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Outlet;

class DailyClosing extends Model
{
    protected $table = 'daily_closings';

    protected $primaryKey = 'id';

    protected $fillable = [
        'outlet_id',
        'cashier_id',
        'shift_number',
        'shift_name',
        'business_date',
        'opened_at',
        'closed_at',
        'starting_cash',
        'system_cash_sales',
        'system_non_cash_sales',
        'cash_in_amount',
        'cash_out_amount',
        'system_expected_cash',
        'actual_cash_counted',
        'cash_difference',
        'notes',
        'status',
    ];

    protected $casts = [
        'business_date' => 'date',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'starting_cash' => 'float',
        'system_cash_sales' => 'float',
        'system_non_cash_sales' => 'float',
        'cash_in_amount' => 'float',
        'cash_out_amount' => 'float',
        'system_expected_cash' => 'float',
        'actual_cash_counted' => 'float',
        'cash_difference' => 'float',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'daily_closing_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'daily_closing_id', 'id');
    }
}
