<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as BaseRole;
use App\Traits\Filterable;

class Role extends BaseRole
{
    use SoftDeletes, Filterable;
}
