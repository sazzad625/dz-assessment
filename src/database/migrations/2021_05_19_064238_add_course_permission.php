<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoursePermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $role = Role::where('name', Role::ADMIN_ROLE)->first();

        $permission = new Permission();
        $permission->name = Permission::CREATE_COURSE_PERMISSION;
        $permission->save();
        $role->permissions()->attach($permission);

        $permission = new Permission();
        $permission->name = Permission::UPDATE_COURSE_PERMISSION;
        $permission->save();
        $role->permissions()->attach($permission);

        $permission = new Permission();
        $permission->name = Permission::DELETE_COURSE_PERMISSION;
        $permission->save();
        $role->permissions()->attach($permission);

        $permission = new Permission();
        $permission->name = Permission::VIEW_COURSE_PERMISSION;
        $permission->save();
        $role->permissions()->attach($permission);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $role = Role::where('name', Role::ADMIN_ROLE)->first();

        $permission = Permission::where('name', Permission::CREATE_COURSE_PERMISSION)->first();
        $role->permissions()->detach($permission);
        $permission->forceDelete();

        $permission = Permission::where('name', Permission::UPDATE_COURSE_PERMISSION)->first();
        $role->permissions()->detach($permission);
        $permission->forceDelete();

        $permission = Permission::where('name', Permission::DELETE_COURSE_PERMISSION)->first();
        $role->permissions()->detach($permission);
        $permission->forceDelete();

        $permission = Permission::where('name', Permission::VIEW_COURSE_PERMISSION)->first();
        $role->permissions()->detach($permission);
        $permission->forceDelete();
    }
}
