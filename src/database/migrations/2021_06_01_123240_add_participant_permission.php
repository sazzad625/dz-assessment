<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParticipantPermission extends Migration
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
        $permission->name = Permission::SEARCH_COURSE_PARTICIPANT_PERMISSION;
        $permission->save();
        $permission->roles()->attach($role);

        $permission = new Permission();
        $permission->name = Permission::ENROLL_COURSE_PARTICIPANT_PERMISSION;
        $permission->save();
        $permission->roles()->attach($role);

        $permission = new Permission();
        $permission->name = Permission::REMOVE_COURSE_PARTICIPANT_PERMISSION;
        $permission->save();
        $permission->roles()->attach($role);

        $permission = new Permission();
        $permission->name = Permission::BULK_UPLOAD_COURSE_PARTICIPANT_PERMISSION;
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

        $permission = Permission::where('name', Permission::SEARCH_COURSE_PARTICIPANT_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();

        $permission = Permission::where('name', Permission::ENROLL_COURSE_PARTICIPANT_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();

        $permission = Permission::where('name', Permission::REMOVE_COURSE_PARTICIPANT_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();

        $permission = Permission::where('name', Permission::BULK_UPLOAD_COURSE_PARTICIPANT_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();
    }
}
