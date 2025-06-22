<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    const ADMIN = 1;
    const PHOTOGRAPHER = 2;
    const CLIENT = 3;
}
