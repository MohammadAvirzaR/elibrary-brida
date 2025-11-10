<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',        // nama peran: Admin, Petugas, Mahasiswa, dst
        'description', // deskripsi singkat peran
        'permissions', // JSON array of permissions
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Relasi: Role -> Users
     * Satu role bisa dimiliki banyak user.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relasi many-to-many ke privileges (jika fitur hak akses per role digunakan)
     */
    public function privileges()
    {
        return $this->belongsToMany(Privilege::class, 'role_privilege');
    }
}
