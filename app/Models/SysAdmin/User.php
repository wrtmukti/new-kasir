<?php

namespace App\Models\SysAdmin;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'client_id',
        'outlet_id',
        'name',
        'email',
        'password',
        'role',
    ];

    public function outlet()
    {
        return $this->belongsTo(\App\Models\Admin\Outlet::class, 'outlet_id', 'outlet_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Tentukan koneksi database secara dinamis berdasarkan sesi client yang aktif atau context koneksi.
     */
    public function getConnectionName()
    {
        $clientDb = session('client_database') 
            ?? session('tenant_database') 
            ?? \App\Services\Client\ClientDatabaseManager::getCurrentClientDatabase();

        if ($clientDb) {
            $connected = true;
            if (\App\Services\Client\ClientDatabaseManager::getCurrentClientDatabase() !== $clientDb) {
                $connected = \App\Services\Client\ClientDatabaseManager::connectToClient($clientDb);
            }
            if ($connected) {
                return 'client';
            }
        }

        return config('database.default');
    }
}
