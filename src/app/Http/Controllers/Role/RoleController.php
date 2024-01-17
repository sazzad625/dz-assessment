<?php


namespace App\Http\Controllers\Role;

use App\Helpers\AuthHelper;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view(Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::VIEW_ROLE_PERMISSION);

        $roles = Role::select('id', 'name');

        if (!empty(($request->name))) {
            $roles = $roles->where('name', 'like', "%{$request->name}%");
        }

        $roles = $roles->paginate(10);

        return view('role.search_role', ['roles' => $roles]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        AuthHelper::hasPermissionElseAbort(Permission::CREATE_ROLE_PERMISSION);

        $permissions = $this->getPermissions();
        return view('role.create_role', ['permissions' => $permissions]);
    }

    /**
     * @return mixed
     */
    private function getPermissions()
    {
        return Permission::select('id', 'name')->get();
    }

    /**
     * @param Request $request
     */
    public function fetchRoles(Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::VIEW_ROLE_PERMISSION);

        if ($request->get('query')){
            $query = $request->get('query');
            $roles = Role::where('name', 'like', "%{$query}%")->get();
            $output = '<ul class="list-group" style="  z-index: 99999; position: absolute">';
            foreach($roles as $role)
            {
                $output .= '<li class="list-group-item"><a href="#">'.$role->name.'</a></li>';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleCreate(Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::CREATE_ROLE_PERMISSION);

        $messages = [
            'permissions.required' => 'The :attribute must be selected.',
        ];
        $rules = [
            'name'        => 'required|unique:roles|max:255',
            'permissions' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->route('role.create')
                ->withErrors($validator)
                ->withInput();
        }

        $new_role             = new Role();
        $new_role->name       = $request->name;
        $new_role->created_at = Carbon::now();
        $new_role->updated_at = Carbon::now();
        $new_role->save();

        foreach ($request->permissions as $per) {
            $new_role->permissions()->attach($per);
        }

        return redirect()->route('role.create')->with('success', 'Role has been created successfully!');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update($id)
    {
        AuthHelper::hasPermissionElseAbort(Permission::UPDATE_ROLE_PERMISSION);

        $role = Role::findOrFail($id);

        $role->load('permissions');
        $rolePermission = array_column($role->permissions->toArray(), 'id');

        $permissions = $this->getPermissions()->toArray();

        return view('role.update_role',
            ['role' => $role, 'rolePermission' => $rolePermission, 'permissions' => $permissions]);
    }

    /**
     * @param Request $request
     * @param         $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleUpdate(Request $request, $id)
    {
        AuthHelper::hasPermissionElseAbort(Permission::UPDATE_ROLE_PERMISSION);

        $role = Role::findOrFail($id);

        $role->load('permissions');

        $messages = [
            'permissions.required' => 'The :attribute must be selected.',
        ];
        $rules = [
            'permissions' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->route('role.update', $id)
                ->withErrors($validator)
                ->withInput();
        }
        foreach ($role->permissions as $permission){
            $role->permissions()->detach($permission);
        }
        foreach ($request->permissions as $per) {
            $role->permissions()->attach($per);
        }

        return redirect()->route('role.view')->with('success', 'Role has been updated successfully!');
    }
}
