<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi massal.
     */
    protected $fillable = [
        'role_id',
        'full_name',
        'name', // For frontend compatibility
        'username',
        'email',
        'password',
        'sso_id',
        'unit_name',
        'institution', // For frontend compatibility
        'contact',
        'phone', // For frontend compatibility
        'address', // For frontend compatibility
        'bio',
        'membership_proof',
        'profession',
    ];

    /**
     * Kolom yang disembunyikan saat dikirim ke response (misalnya ke API).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data otomatis.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Hash password secara otomatis setiap kali diset.
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    /**
     * Relasi: User -> Role
     * Satu user punya satu role.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relasi: User -> Document
     * Satu user bisa upload banyak dokumen.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Relasi: User -> Notification
     * Satu user bisa punya banyak notifikasi.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Cek apakah user memiliki role tertentu.
     */
    public function hasRole($roleName)
    {
        return $this->role && strtolower($this->role->name) === strtolower($roleName);
    }
}
