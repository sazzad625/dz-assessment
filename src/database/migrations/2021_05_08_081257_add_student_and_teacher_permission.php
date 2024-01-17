<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentAndTeacherPermission extends Migration
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
        $permission->name = Permission::VIEW_STUDENT_PERMISSION;
        $permission->save();
        $permission->roles()->attach($role);

        $permission = new Permission();
        $permission->name = Permission::DELETE_STUDENT_PERMISSION;
        $permission->save();
        $permission->roles()->attach($role);

        $permission = new Permission();
        $permission->name = Permission::BULK_UPLOAD_STUDENT_PERMISSION;
        $permission->save();
        $permission->roles()->attach($role);

        $permission = new Permission();
        $permission->name = Permission::VIEW_TEACHER_PERMISSION;
        $permission->save();
        $permission->roles()->attach($role);
        
        $permission = new Permission();
        $permission->name = Permission::CREATE_TEACHER_PERMISSION;
        $permission->save();
        $permission->roles()->attach($role);

        $permission = new Permission();
        $permission->name = Permission::UPDATE_TEACHER_PERMISSION;
        $permission->save();
        $permission->roles()->attach($role);

        $permission = new Permission();
        $permission->name = Permission::DELETE_TEACHER_PERMISSION;
        $permission->save();
        $permission->roles()->attach($role);

        $permission = new Permission();
        $permission->name = Permission::BULK_UPLOAD_TEACHER_PERMISSION;
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

        $permission = Permission::where('name', Permission::VIEW_STUDENT_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();
        
        $permission = Permission::where('name', Permission::DELETE_STUDENT_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();

        $permission = Permission::where('name', Permission::BULK_UPLOAD_STUDENT_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();

        $permission = Permission::where('name', Permission::VIEW_TEACHER_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();

        $permission = Permission::where('name', Permission::CREATE_TEACHER_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();

        $permission = Permission::where('name', Permission::UPDATE_TEACHER_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();
        
        $permission = Permission::where('name', Permission::DELETE_TEACHER_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();

        $permission = Permission::where('name', Permission::BULK_UPLOAD_TEACHER_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();
    }
}
