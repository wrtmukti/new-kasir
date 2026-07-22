<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;

class Customer extends Model
{
    protected $table = 'customers';

    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'company_id',
        'transaction_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'customer_notes',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}
