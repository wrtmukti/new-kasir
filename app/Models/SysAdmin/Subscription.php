<?php

namespace App\Models\SysAdmin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $connection = 'central';
    protected $table = 'subscriptions';

    protected $fillable = [
        'subscription_id',
        'client_id',
        'plan_id',
        'start_date',
        'expired_date',
        'status',
        'billing_reference',
        'amount_paid',
        'payment_method',
        'paid_at',
        'auto_renew',
        'notes',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expired_date' => 'date',
        'amount_paid' => 'float',
        'paid_at' => 'datetime',
        'auto_renew' => 'integer',
        'delete_status' => 'integer',
    ];

    /**
     * Relasi ke Client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    /**
     * Relasi ke Plan
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }

    /**
     * Cek apakah masa berlaku sudah kadaluarsa
     */
    public function isExpired(): bool
    {
        return $this->expired_date < now()->toDateString() || $this->status === 'expired';
    }

    /**
     * Hitung sisa hari aktif
     */
    public function daysRemaining(): int
    {
        return max(0, now()->diffInDays($this->expired_date, false));
    }
}
