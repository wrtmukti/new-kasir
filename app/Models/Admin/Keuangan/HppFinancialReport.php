<?php

namespace App\Models\Admin\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;

class HppFinancialReport extends Model
{
    use HasFactory;

    protected $table = 'hpp_financial_reports';
    protected $primaryKey = 'hpp_financial_report_id';

    protected $fillable = [
        'company_id',
        'year',
        'month',
        'total_revenue',
        'total_cogs_estimated',
        'total_waste_cost',
        'total_labor_cost',
        'total_overhead_cost',
        'gross_profit',
        'net_profit_estimated',
        'notes',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}
