<?php

namespace App\Models\SysAdmin;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $connection = 'central';
    protected $table = 'system_settings';

    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_group',
        'display_name',
        'description',
        'created_by',
        'updated_by',
    ];

    /**
     * Ambil value setting by key dengan default fallback
     */
    public static function getVal(string $key, mixed $default = null): mixed
    {
        $setting = self::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    /**
     * Simpan / Update setting
     */
    public static function setVal(string $key, mixed $value, string $group = 'general', ?string $displayName = null): self
    {
        return self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'setting_group' => $group,
                'display_name' => $displayName ?? ucwords(str_replace('_', ' ', $key)),
            ]
        );
    }
}
