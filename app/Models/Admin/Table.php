<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Outlet;

class Table extends Model
{
    use HasUlids;

    protected $table = 'tables';

    protected $primaryKey = 'table_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'table_id',
        'outlet_id',
        'table_number',
        'table_status',
        'table_capacity',
        'table_description',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }

    /**
     * Ambil outlet_id yang valid (dengan fallback ke outlet pertama).
     */
    public function getEffectiveOutletIdAttribute(): string
    {
        if (!empty($this->outlet_id)) {
            return (string) $this->outlet_id;
        }
        return (string) (Outlet::where('delete_status', 0)->value('outlet_id') ?? 'default');
    }

    /**
     * Generate Link Akses Menu Meja untuk Guest: /{client_id}/{outlet_id}/{table_id}
     */
    public function getGuestMenuUrlAttribute(): string
    {
        $clientId = session('client_id') ?? session('tenant_client_id') ?? '';
        if (empty($clientId) && session('client_database')) {
            $client = \App\Models\SysAdmin\Client::where('database_name', session('client_database'))->first();
            $clientId = $client?->client_id ?? '';
        }
        $outletId = $this->effective_outlet_id;
        return url("{$clientId}/{$outletId}/{$this->table_id}");
    }
}
