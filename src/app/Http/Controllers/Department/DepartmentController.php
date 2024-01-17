<?php

namespace App\Http\Controllers\Department;

use App\Helpers\AuthHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Http\Requests\Department\AddDepartmentRequest;
use App\Models\Department;
use App\Models\Permission;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function search()
    {
        AuthHelper::hasPermissionElseAbort(Permission::SEARCH_DEPARTMENT_PERMISSION);

        $department = Department::get();
        return view('department.search', ['departments' => $department]);
    }

    public function addDepartment()
    {
        AuthHelper::hasPermissionElseAbort(Permission::CREATE_DEPARTMENT_PERMISSION);

        return view('department.add');
    }

    public function handleAddDepartment(AddDepartmentRequest $request)
    {
        $courseCategory = new Department();
        $courseCategory->name = $request->name;
        $courseCategory->save();

        return redirect(route('department.update', $courseCategory->id))->with('status.success', 'Department created successfully');;
    }

    public function updateDepartment($id)
    {
        AuthHelper::hasPermissionElseAbort(Permission::UPDATE_DEPARTMENT_PERMISSION);

        $department= Department::findOrFail($id);

        return view('department.update', [
            'row' => $department
        ]);

    }

    public function handleUpdateDepartment($id, UpdateDepartmentRequest $request)
    {
        $department = Department::find($id);
        $department->name = $request->name;
        $department->save();

        return redirect(route('department.update', $department->id))->with('status.success','Department updated successfully');
    }

    public function deleteDepartment(Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::DELETE_DEPARTMENT_PERMISSION);

        $request->validate(['id' => 'required']);
        Department::destroy($request->id);
        return Response::json(['message' => 'Record deleted successfully'], 200);

    }
}
