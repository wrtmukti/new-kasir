<?php

namespace App\Models\SysAdmin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DatabaseConnection extends Model
{
    protected $connection = 'central';
    protected $table = 'database_connections';

    protected $fillable = [
        'client_id',
        'database_name',
        'server_host',
        'server_port',
        'connection_status',
        'latency_ms',
        'database_size_mb',
        'tables_count',
        'migration_version',
        'last_health_check_at',
        'last_backup_at',
        'status_message',
        'delete_status',
    ];

    protected $casts = [
        'server_port' => 'integer',
        'latency_ms' => 'float',
        'database_size_mb' => 'float',
        'tables_count' => 'integer',
        'last_health_check_at' => 'datetime',
        'last_backup_at' => 'datetime',
        'delete_status' => 'integer',
    ];

    /**
     * Relasi ke Client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }
}
