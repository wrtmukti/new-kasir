<?php

namespace App\Models\SysAdmin;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;
    protected $connection = 'central';
    protected $table = 'audit_logs';

    protected $fillable = [
        'actor_type',
        'actor_id',
        'actor_name',
        'actor_role',
        'client_id',
        'outlet_id',
        'action',
        'target_type',
        'target_id',
        'ip_address',
        'user_agent',
        'result',
        'metadata_json',
        'created_at',
    ];

    protected $casts = [
        'metadata_json' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Helper static untuk mencatat log dengan mudah
     */
    public static function record(
        string $action,
        ?string $clientId = null,
        ?string $targetType = null,
        ?string $targetId = null,
        string $result = 'success',
        ?array $metadata = null,
        ?string $outletId = null
    ): self {
        $user = auth('system_admin')->user() ?? auth()->user();

        return self::create([
            'actor_type' => $user ? 'system_user' : 'system_cron',
            'actor_id' => $user?->id,
            'actor_name' => $user?->name ?? 'System',
            'actor_role' => $user?->role ?? 'system',
            'client_id' => $clientId,
            'outlet_id' => $outletId,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'user_agent' => request()->userAgent() ?? 'CLI/System',
            'result' => $result,
            'metadata_json' => $metadata,
            'created_at' => now(),
        ]);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }
}
