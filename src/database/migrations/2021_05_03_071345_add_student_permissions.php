<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentPermissions extends Migration
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
        $permission->name = Permission::CREATE_STUDENT_PERMISSION;
        $permission->save();
        $role->permissions()->attach($permission);

        $permission = new Permission();
        $permission->name = Permission::UPDATE_STUDENT_PERMISSION;
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

        $permission = Permission::where('name', Permission::CREATE_STUDENT_PERMISSION)->first();
        $role->permissions()->detach($permission);
        $permission->forceDelete();

        $permission = Permission::where('name', Permission::UPDATE_STUDENT_PERMISSION)->first();
        $role->permissions()->detach($permission);
        $permission->forceDelete();
    }
}
