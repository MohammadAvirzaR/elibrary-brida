<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Model Role yang extends Spatie Role.
 * Menambahkan field description dan guard_name default 'api'.
 */
class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'description',
    ];

    /**
     * Guard name default untuk API.
     */
    protected $guard_name = 'api';
}
