<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Privilege extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',        // nama privilege, misal: manage_users, view_documents, delete_reviews
        'description', // deskripsi singkat
    ];

    /**
     * Relasi many-to-many ke roles melalui pivot table role_privilege
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_privilege');
    }
}
