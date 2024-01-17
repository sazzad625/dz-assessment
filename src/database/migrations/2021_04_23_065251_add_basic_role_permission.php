<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

class AddBasicRolePermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $role = new Role();
        $role->name = Role::ADMIN_ROLE;
        $role->save();

        $permission = Permission::where('name', Permission::CREATE_COURSE_CATEGORY_PERMISSION)->first();
        $permission->roles()->attach($role);

        $permission = Permission::where('name', Permission::UPDATE_COURSE_CATEGORY_PERMISSION)->first();
        $permission->roles()->attach($role);

        $user = User::withTrashed()->find(1);
        $user->roles()->attach($role);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $role = Role::where('name', Role::ADMIN_ROLE)->first();
        $user = User::find(1);

        $user->roles()->detach();
        $permission = Permission::where('name', Permission::CREATE_COURSE_CATEGORY_PERMISSION)->first();
        $permission->roles()->detach($role);

        $permission = Permission::where('name', Permission::UPDATE_COURSE_CATEGORY_PERMISSION)->first();
        $permission->roles()->detach($role);

        $role->forceDelete();
    }
}
