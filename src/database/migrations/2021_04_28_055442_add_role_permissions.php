<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRolePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permission = new Permission();
        $permission->name = Permission::CREATE_ROLE_PERMISSION;
        $permission->save();

        $permission = new Permission();
        $permission->name = Permission::UPDATE_ROLE_PERMISSION;
        $permission->save();
        
        $permission = new Permission();
        $permission->name = Permission::VIEW_ROLE_PERMISSION;
        $permission->save();

        $role = Role::where('name', Role::ADMIN_ROLE)->first();

        $permission = Permission::where('name', Permission::CREATE_ROLE_PERMISSION)->first();
        $permission->roles()->attach($role);

        $permission = Permission::where('name', Permission::UPDATE_ROLE_PERMISSION)->first();
        $permission->roles()->attach($role);

        $permission = Permission::where('name', Permission::VIEW_ROLE_PERMISSION)->first();
        $permission->roles()->attach($role);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $role = Role::where('name', Role::ADMIN_ROLE)->first();
        
        $permission = Permission::where('name', Permission::CREATE_ROLE_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();

        $permission = Permission::where('name', Permission::UPDATE_ROLE_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();
        
        $permission = Permission::where('name', Permission::VIEW_ROLE_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();
    }
}
