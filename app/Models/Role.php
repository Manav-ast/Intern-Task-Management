<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $guard_name = 'admin';

    protected $fillable = [
        'name',
        'guard_name',
        'slug',
        'description',
    ];
}
