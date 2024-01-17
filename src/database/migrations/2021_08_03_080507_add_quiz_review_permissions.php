<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuizReviewPermissions extends Migration
{
    private $permissions = [
        Permission::SEARCH_QUIZ_REVIEW_PERMISSION,
        Permission::QUIZ_REVIEW_PERMISSION
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
