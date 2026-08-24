<?php

namespace App\Models\SysAdmin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $connection = 'central';
    protected $table = 'plans';

    protected $fillable = [
        'plan_code',
        'plan_name',
        'badge_label',
        'description',
        'max_outlets',
        'max_users',
        'max_storage_mb',
        'features_json',
        'price_monthly',
        'price_yearly',
        'trial_days',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    protected $casts = [
        'max_outlets' => 'integer',
        'max_users' => 'integer',
        'max_storage_mb' => 'integer',
        'features_json' => 'array',
        'price_monthly' => 'float',
        'price_yearly' => 'float',
        'trial_days' => 'integer',
        'is_active' => 'integer',
        'sort_order' => 'integer',
        'delete_status' => 'integer',
    ];

    /**
     * Relasi ke Subscriptions yang menggunakan plan ini
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id', 'id');
    }

    /**
     * Cek apakah fitur tertentu aktif di paket ini
     */
    public function hasFeature(string $featureKey): bool
    {
        $features = $this->features_json ?? [];
        return !empty($features[$featureKey]);
    }
}
