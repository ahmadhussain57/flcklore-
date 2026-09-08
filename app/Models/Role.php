<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Concerns\HasUlids;


class Role extends SpatieRole
{
    use HasUlids;
    protected $fillable = ['name', 'guard_name', 'section', 'updated_at', 'created_at'];
}
