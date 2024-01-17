<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddViewCategoryPermission extends Migration
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
        $permission->name = Permission::VIEW_COURSE_CATEGORY_PERMISSION;
        $permission->save();
        $permission->roles()->attach($role);

        $permission = new Permission();
        $permission->name = Permission::DELETE_COURSE_CATEGORY_PERMISSION;
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
        
        $permission = Permission::where('name', Permission::VIEW_COURSE_CATEGORY_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();

        $permission = Permission::where('name', Permission::DELETE_COURSE_CATEGORY_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();
    }
}
