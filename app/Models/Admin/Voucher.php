<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;

class Voucher extends Model
{
    protected $table = 'vouchers';

    protected $primaryKey = 'voucher_id';

    protected $fillable = [
        'company_id',
        'voucher_code',
        'voucher_name',
        'voucher_description',
        'voucher_type',
        'voucher_value',
        'voucher_max_discount',
        'voucher_min_purchase',
        'voucher_applicable_to',
        'voucher_usage_limit',
        'voucher_usage_per_customer',
        'voucher_start_date',
        'voucher_end_date',
        'voucher_status',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function scopeActive($query)
    {
        return $query->where('delete_status', 0)
            ->where('voucher_status', 1)
            ->where('voucher_start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('voucher_end_date')
                  ->orWhere('voucher_end_date', '>=', now());
            });
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('voucher_code', $code);
    }
}
