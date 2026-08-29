<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Outlet extends Model
{
    use HasFactory;

    protected $table = 'outlets';
    protected $primaryKey = 'outlet_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'outlet_id',
        'outlet_name',
        'outlet_code',
        'outlet_branch',
        'outlet_slug',
        'outlet_email',
        'outlet_phone',
        'outlet_address',
        'outlet_image',
        'outlet_status',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    protected $casts = [
        'outlet_status' => 'integer',
        'delete_status' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->outlet_id)) {
                $model->outlet_id = (string) \Illuminate\Support\Str::ulid();
            }
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'outlet_id', 'outlet_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'outlet_id', 'outlet_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'outlet_id', 'outlet_id');
    }

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class, 'outlet_id', 'outlet_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'outlet_id', 'outlet_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'outlet_id', 'outlet_id');
    }

    public function settingOutlet(): HasOne
    {
        return $this->hasOne(SettingOutlet::class, 'outlet_id', 'outlet_id');
    }

    public function shiftSetting(): HasOne
    {
        return $this->hasOne(ShiftSetting::class, 'outlet_id', 'outlet_id');
    }

    public function dailyClosings(): HasMany
    {
        return $this->hasMany(DailyClosing::class, 'outlet_id', 'outlet_id');
    }
}
