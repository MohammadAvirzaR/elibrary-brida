<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Guard name yang digunakan untuk Spatie Permission.
     */
    protected $guard_name = 'api';

    /**
     * Kolom yang boleh diisi massal.
     */
    protected $fillable = [
        'full_name',
        'username',
        'email',
        'password',
        'sso_id',
        'unit_name',
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
     * Accessor: returns first Spatie Role object.
     * Backward-compatible with code that uses $user->role->name.
     * Use ->with('roles') in eager loading instead of ->with('role').
     */
    public function getRoleAttribute(): ?\Spatie\Permission\Contracts\Role
    {
        return $this->roles->first();
    }

    /**
     * Accessor untuk mendapatkan nama role pertama user.
     * Kompatibel dengan sistem lama.
     */
    public function getRoleNameAttribute(): string
    {
        return $this->roles->first()?->name ?? 'guest';
    }

    /**
     * Accessor untuk mendapatkan nama lengkap (kompatibilitas lama).
     */
    public function getNameAttribute(): string
    {
        return $this->full_name ?? $this->attributes['name'] ?? '';
    }

    /**
     * Accessor untuk institution (kompatibilitas lama).
     */
    public function getInstitutionAttribute(): ?string
    {
        return $this->unit_name ?? null;
    }
}
