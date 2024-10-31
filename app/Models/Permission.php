<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission as BasePermission;
use App\Traits\Filterable;

class Permission extends BasePermission
{
    use SoftDeletes, Filterable;
}
