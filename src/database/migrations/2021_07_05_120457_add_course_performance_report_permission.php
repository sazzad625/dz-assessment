<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

class AddCoursePerformanceReportPermission extends Migration
{
    private $permissions = [
        Permission::COURSE_PERFORMANCE_REPORT_PERMISSION,
        Permission::COURSE_PERFORMANCE_REPORT_EXPORT_PERMISSION,
        Permission::REPORT_DOWNLOAD_PERMISSION,
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $role = Role::where('name', Role::ADMIN_ROLE)->first();

        foreach ($this->permissions as $permissionName) {
            $permission = new Permission();
            $permission->name = $permissionName;
            $permission->save();
            $role->permissions()->attach($permission);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $role = Role::where('name', Role::ADMIN_ROLE)->first();

        foreach ($this->permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            $role->permissions()->detach($permission);
            $permission->forceDelete();
        }
    }
}
