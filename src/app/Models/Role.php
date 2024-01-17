<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $table = "roles";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /*
     * The Permission that belong to the roles.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission',
        'fk_role_id', 'fk_permission_id');
    }

    const ADMIN_ROLE = "ADMIN";
}
