<?php

namespace App\Models\SysAdmin;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SystemUser extends Authenticatable
{
    use Notifiable;

    protected $connection = 'central';
    protected $table = 'system_users';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'created_by',
        'updated_by',
        'delete_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'integer',
        'delete_status' => 'integer',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Helper cek role
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isSystemAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'system_admin']);
    }

    public function isSupport(): bool
    {
        return $this->role === 'support';
    }
}
