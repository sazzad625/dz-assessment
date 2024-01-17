<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndividualPerformanceReportPermission extends Migration
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
        $permission->name = Permission::INDIVIDUAL_PERFORMANCE_REPORT_PERMISSION;
        $permission->save();
        $permission->roles()->attach($role);

        $permission = new Permission();
        $permission->name = Permission::INDIVIDUAL_PERFORMANCE_REPORT_DOWNLOAD_PERMISSION;
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

        $permission = Permission::where('name', Permission::INDIVIDUAL_PERFORMANCE_REPORT_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();

        $permission = Permission::where('name', Permission::INDIVIDUAL_PERFORMANCE_REPORT_DOWNLOAD_PERMISSION)->first();
        $permission->roles()->detach($role);
        $permission->forceDelete();
    }
}
