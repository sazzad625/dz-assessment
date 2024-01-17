<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileManagerPermission extends Migration
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
        $permission->name = Permission::FILE_MANAGER_PERMISSION;
        $permission->save();
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

        $permission = Permission::where('name', Permission::FILE_MANAGER_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();
    }
}
