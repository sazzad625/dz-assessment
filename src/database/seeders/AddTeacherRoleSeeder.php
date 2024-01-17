<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AddTeacherRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = new Role();
        $role->name = "TEACHER";
        $role->save();

        $permissions = Permission::whereIn('name', [
            Permission::CREATE_STUDENT_PERMISSION,
            Permission::UPDATE_STUDENT_PERMISSION,
            Permission::BULK_UPLOAD_STUDENT_PERMISSION,
            Permission::VIEW_STUDENT_PERMISSION
        ])->get();

        $role->permissions()->attach($permissions);

    }
}
