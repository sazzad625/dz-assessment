<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permission = new Permission();
        $permission->name = Permission::CREATE_COURSE_CATEGORY_PERMISSION;
        $permission->save();

        $permission = new Permission();
        $permission->name = Permission::UPDATE_COURSE_CATEGORY_PERMISSION;
        $permission->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permission = Permission::where('name', Permission::UPDATE_COURSE_CATEGORY_PERMISSION)->first();
        $permission->forceDelete();

        $permission = Permission::where('name', Permission::CREATE_COURSE_CATEGORY_PERMISSION)->first();
        $permission->forceDelete();
    }
}
