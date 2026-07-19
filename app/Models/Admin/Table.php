<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\SysAdmin\Company;

class Table extends Model
{
    protected $table = 'tables';

    protected $primaryKey = 'table_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'table_id',
        'company_id',
        'table_number',
        'table_status',
        'table_capacity',
        'table_description',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}
