<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\DailyClosing;
use App\Models\Admin\Outlet;
use App\Models\User;

class CashDrawerLog extends Model
{
    use HasFactory;

    protected $table = 'cash_drawer_logs';

    protected $fillable = [
        'outlet_id',
        'daily_closing_id',
        'cashier_id',
        'type',
        'category',
        'amount',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function dailyClosing()
    {
        return $this->belongsTo(DailyClosing::class, 'daily_closing_id', 'id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id', 'id');
    }
}
