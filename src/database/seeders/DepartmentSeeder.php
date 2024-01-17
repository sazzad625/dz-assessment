<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::find(1);

        $department = new Department();
        $department->name = "test department 1";
        $department->save();

        $department = new Department();
        $department->name = "test department 2";
        $department->save();

    }
}
