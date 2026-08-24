<?php

namespace App\Models\SysAdmin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    protected $connection = 'central';
    protected $table = 'clients';

    protected $fillable = [
        'client_id',
        'client_slug',
        'client_name',
        'client_code',
        'business_name',
        'owner_name',
        'owner_email',
        'owner_phone',
        'address',
        'logo',
        'database_name',
        'db_host',
        'db_port',
        'db_username',
        'db_password',
        'status',
        'suspension_reason',
        'provisioned_at',
        'last_active_at',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    protected $casts = [
        'db_port' => 'integer',
        'provisioned_at' => 'datetime',
        'last_active_at' => 'datetime',
        'delete_status' => 'integer',
    ];

    protected $hidden = [
        'db_password',
    ];

    /**
     * Relasi ke Subscriptions (1 client can have history of subscriptions)
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'client_id', 'client_id');
    }

    /**
     * Relasi ke Subscription yang sedang aktif saat ini
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'client_id', 'client_id')
            ->whereIn('status', ['active', 'trial', 'expiring_soon'])
            ->latest('id');
    }

    /**
     * Relasi ke Database Connection metadata
     */
    public function databaseConnection(): HasOne
    {
        return $this->hasOne(DatabaseConnection::class, 'client_id', 'client_id');
    }

    /**
     * Relasi ke Audit Logs tenant ini
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'client_id', 'client_id');
    }

    /**
     * Scope Client Aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('delete_status', 0);
    }
}
