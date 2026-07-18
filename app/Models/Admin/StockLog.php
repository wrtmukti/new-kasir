<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;

class StockLog extends Model
{
    protected $table = 'stock_logs';
    protected $primaryKey = 'log_id';

    protected $fillable = [
        'company_id', 'stock_id',
        'reference_type', 'reference_code', 'type',
        'qty', 'price', 'total',
        'stock_before', 'stock_after',
        'notes',
        'created_by', 'updated_by', 'delete_status',
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
        'delete_status' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'stock_id');
    }
}
