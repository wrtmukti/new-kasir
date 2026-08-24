<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Outlet;

class Customer extends Model
{
    protected $table = 'customers';

    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'outlet_id',
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

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }
}
