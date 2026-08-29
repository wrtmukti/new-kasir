<?php

namespace App\Models\Admin\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Outlet;

class HppFinancialReport extends Model
{
    use HasFactory;

    protected $table = 'hpp_financial_reports';
    protected $primaryKey = 'hpp_financial_report_id';

    protected $fillable = [
        'outlet_id',
        'year',
        'month',
        'total_revenue',
        'total_cogs_estimated',
        'total_waste_cost',
        'total_labor_cost',
        'total_overhead_cost',
        'gross_profit',
        'gross_margin_percent',
        'net_profit_estimated',
        'net_margin_percent',
        'notes',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }
}
