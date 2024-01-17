<?php

namespace App\Http\Controllers\User;

use App\Helpers\AuthHelper;
use App\Helpers\OssStorageHelper;
use App\Helpers\QueueHelper;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    const UPLOAD_USER_TEMPLATE_PATH = 'template/UploadUsersTemplate.csv';

    public function index($type)
    {
        $type = strtoupper($type);

        switch ($type) {
            case User::USER_TYPE_STUDENT :
            {
                AuthHelper::hasPermissionElseAbort(Permission::CREATE_STUDENT_PERMISSION);
                return view('user.add-student');
                break;
            }
            case User::USER_TYPE_TEACHER :
            {
                AuthHelper::hasPermissionElseAbort(Permission::CREATE_TEACHER_PERMISSION);
                return view('user.add-teacher');
                break;
            }
            default :
            {
                abort(404);
            }
        }

    }

    public function delete($type, Request $request)
    {

        $type = strtoupper($type);
        switch ($type) {
            case User::USER_TYPE_TEACHER:
            {
                AuthHelper::hasPermissionElseAbort(Permission::DELETE_TEACHER_PERMISSION);
                return $this->deleteTeacher($request);
                break;
            }
            case User::USER_TYPE_STUDENT:
            {
                AuthHelper::hasPermissionElseAbort(Permission::DELETE_STUDENT_PERMISSION);
                return $this->deleteStudent($request);
                break;
            }
            default:
            {
                abort(404);
            }
        }
    }

    public function handleAddUser($type, Request $request)
    {
        $type = strtoupper($type);

        switch ($type) {
            case User::USER_TYPE_STUDENT :
            {
                AuthHelper::hasPermissionElseAbort(Permission::CREATE_STUDENT_PERMISSION);
                return $this->handleAddStudent($request);
                break;
            }
            case User::USER_TYPE_TEACHER :
            {
                AuthHelper::hasPermissionElseAbort(Permission::CREATE_TEACHER_PERMISSION);
                return $this->handleAddTeacher($request);
                break;
            }
            default :
            {
                abort(404);
            }
        }

    }

    public function updateUser($type, $id)
    {
        $type = strtoupper($type);

        switch ($type) {
            case User::USER_TYPE_STUDENT :
            {
                AuthHelper::hasPermissionElseAbort(Permission::UPDATE_STUDENT_PERMISSION);
                return $this->updateStudent($id);
                break;
            }
            case User::USER_TYPE_TEACHER :
            {
                AuthHelper::hasPermissionElseAbort(Permission::UPDATE_TEACHER_PERMISSION);
                return $this->updateTeacher($id);
                break;
            }
            default :
            {
                abort(404);
            }
        }
    }

    public function handleUpdateUser($type, $id, Request $request)
    {
        $type = strtoupper($type);

        switch ($type) {
            case User::USER_TYPE_STUDENT :
            {
                AuthHelper::hasPermissionElseAbort(Permission::UPDATE_STUDENT_PERMISSION);
                return $this->handleUpdateStudent($id, $request);
                break;
            }
            case User::USER_TYPE_TEACHER :
            {
                AuthHelper::hasPermissionElseAbort(Permission::UPDATE_TEACHER_PERMISSION);
                return $this->handleUpdateTeacher($id, $request);
                break;
            }
            default :
            {
                abort(404);
            }
        }

    }

    private function handleAddStudent(Request $request)
    {
        $request->validate([
            'employeeId' => 'required|max:20|unique:users,employee_id',
            'wfmId' => 'required|max:20|unique:users,wfm_id',
            'firstName' => 'required|min:3|max:50',
            'lastName' => 'required|min:3|max:50',
            'name' => 'required|min:3|max:60|unique:users,name',
            'password' => 'required|min:8|max:25',
            'country' => 'required',
            'department' => 'required',
            'hub_name' => 'required',
            'city_name' => 'required'
        ]);

        $user = new User();
        $user->employee_id = $request->employeeId;
        $user->wfm_id = $request->wfmId;
        $user->first_name = $request->firstName;
        $user->last_name = $request->lastName;
        $user->name = $request->name;
        $user->email = $request->name . "@domain.com";
        $user->password = bcrypt($request->password);
        $user->type = User::USER_TYPE_STUDENT;
        $user->fk_country_id = $request->country;
        $user->fk_department_id = $request->department;
        $user->hub_name = $request->hub_name;
        $user->city_name = $request->city_name;

        $user->save();

        return redirect(route('user.update',
            [strtolower(User::USER_TYPE_STUDENT), $user->id]))->with('status.success', 'Student created successfully');

    }

    private function handleAddTeacher(Request $request)
    {
        $request->validate([
            'employeeId' => "required|max:20|unique:users,employee_id",
            'firstName' => 'required|min:3|max:50',
            'lastName' => 'required|min:3|max:50',
            'name' => 'required|min:3|max:60|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'country' => 'required',
            'role' => 'required'
        ]);

        $user = new User();
        $user->employee_id = $request->employeeId;
        $user->first_name = $request->firstName;
        $user->last_name = $request->lastName;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = "123";
        $user->type = User::USER_TYPE_TEACHER;
        $user->fk_country_id = $request->country;

        DB::beginTransaction();
        try {
            $user->save();
            $user->roles()->attach(Role::find($request->role));
            DB::commit();
            $user->sendSetPasswordMail();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('status.error', "Unable to add teacher");
        }

        return redirect(route('user.update',
            [strtolower(User::USER_TYPE_TEACHER), $user->id]))->with('status.success', 'Teacher created successfully');

    }

    private function updateStudent($id)
    {
        $user = User::findOrFail($id);
        if ($user->type != User::USER_TYPE_STUDENT) {
            abort(404);
        }

        return view('user.update-student', ['user' => $user]);
    }

    private function updateTeacher($id)
    {
        $user = User::findOrFail($id);
        $user->load('roles');
        if ($user->type != User::USER_TYPE_TEACHER) {
            abort(404);
        }

        return view('user.update-teacher', ['user' => $user]);
    }

    private function handleUpdateStudent($id, Request $request)
    {
        $user = User::findOrFail($id);
        if ($user->type != User::USER_TYPE_STUDENT) {
            abort(404);
        }

        $request->validate([
            'employeeId' => "required|max:20|unique:users,employee_id,{$id}",
            'wfmId' => "required|max:20|unique:users,wfm_id,{$id}",
            'firstName' => 'required|min:3|max:50',
            'lastName' => 'required|min:3|max:50',
            'name' => 'required|min:3|max:60|unique:users,name,' . $request->route('id') . ',id,deleted_at,NULL',
            'country' => 'required',
            'department' => 'required',
            'city_name' => 'required',
            'hub_name' => 'required',
        ]);

        $user->employee_id = $request->employeeId;
        $user->wfm_id = $request->wfmId;
        $user->first_name = $request->firstName;
        $user->last_name = $request->lastName;
        $user->name = $request->name;
        $user->fk_country_id = $request->country;
        $user->fk_department_id = $request->department;
        $user->city_name = $request->city_name;
        $user->hub_name = $request->hub_name;

        $user->save();

        return redirect(route('user.update',
            [strtolower(User::USER_TYPE_STUDENT), $id]))->with('status.success', 'Student updated successfully');
    }

    private function handleUpdateTeacher($id, Request $request)
    {
        $user = User::findOrFail($id);
        $user->load('roles');
        if ($user->type != User::USER_TYPE_TEACHER) {
            abort(404);
        }

        $request->validate([
            'employeeId' => "required|max:20|unique:users,employee_id,{$id}",
            'firstName' => 'required|min:3|max:50',
            'lastName' => 'required|min:3|max:50',
            'name' => 'required|min:3|max:60|unique:users,name,' . $request->route('id') . ',id,deleted_at,NULL',
            'country' => 'required',
            'role' => 'required'
        ]);

        $user->employee_id = $request->employeeId;
        $user->first_name = $request->firstName;
        $user->last_name = $request->lastName;
        $user->name = $request->name;
        $user->fk_country_id = $request->country;

        //only update role if its changed to save db query hit
        if ($user->roles->first()->id != $request->role) {
            $user->roles()->detach();
            $user->roles()->attach(Role::find($request->role));
        }

        $user->save();

        return redirect(route('user.update',
            [strtolower(User::USER_TYPE_TEACHER), $id]))->with('status.success', 'Teacher updated successfully');
    }

    public function bulkUploadUser($type)
    {
        $type = strtoupper($type);

        switch ($type) {
            case User::USER_TYPE_STUDENT:
            {
                AuthHelper::hasPermissionElseAbort(Permission::BULK_UPLOAD_STUDENT_PERMISSION);
                return $this->returnUploadUserView($type);
            }
            case User::USER_TYPE_TEACHER:
            {
                AuthHelper::hasPermissionElseAbort(Permission::BULK_UPLOAD_TEACHER_PERMISSION);
                return $this->returnUploadUserView($type);
            }
            default:
            {
                abort(404);
            }
        }
    }

    public function handleBulkUploadUser($type, Request $request)
    {
        $type = strtoupper($type);

        switch ($type) {
            case User::USER_TYPE_STUDENT:
            {
                AuthHelper::hasPermissionElseAbort(Permission::BULK_UPLOAD_STUDENT_PERMISSION);
                return $this->handleBulkUploadUsers($type, $request);
            }
            case User::USER_TYPE_TEACHER:
            {
                AuthHelper::hasPermissionElseAbort(Permission::BULK_UPLOAD_TEACHER_PERMISSION);
                return $this->handleBulkUploadUsers($type, $request);
            }
            default:
            {
                abort(404);
            }
        }
    }

    private function returnUploadUserView($type)
    {
        return view('user.upload', [
            'type' => $type
        ]);
    }

    private function handleBulkUploadUsers($type, Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048'
        ]);
        $filePath = OssStorageHelper::storeUploadedFileToTemp($request->file('file'));
        Artisan::queue('import:users',
            [
                'filePath' => $filePath['path'] . $filePath['name'],
                'userId' => Auth::id(),
                'type' => $type
            ]
        )->onQueue(QueueHelper::IMPORT_USER_QUEUE);

        return back()->with('success', 'File has uploaded sucessfully');
    }

    public function search($type, Request $request)
    {
        $type = strtoupper($type);

        switch ($type) {
            case User::USER_TYPE_TEACHER :
            {
                AuthHelper::hasPermissionElseAbort(Permission::SEARCH_TEACHER_PERMISSION);
                return $this->searchTeacher($request);
                break;
            }
            case User::USER_TYPE_STUDENT :
            {
                AuthHelper::hasPermissionElseAbort(Permission::SEARCH_STUDENT_PERMISSION);
                return $this->searchStudent($request);
                break;
            }
            default :
            {
                abort(404);
            }
        }
    }

    private function searchTeacher(Request $request)
    {
        $search = User::with('roles', 'country')
            ->where('type', User::USER_TYPE_TEACHER);

        if (!empty($request->employeeId)) {
            $search = $search->where('employee_id', 'like', "%$request->employeeId%");
        }

        if (!empty($request->country)) {
            $search = $search->where('fk_country_id', $request->country);
        }

        if (!empty($request->name)) {
            $search = $search->where('name', 'like', "%{$request->name}%");
        }

        return view('user.search-teacher', [
            'search' => $search->paginate()
        ]);

    }

    private function deleteTeacher(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $user = User::findOrFail($request->id);
        if ($user->type != User::USER_TYPE_TEACHER) {
            abort(404);
        }

        $user->delete();

        return Response::json([
            'message' => "success"
        ]);
    }

    private function searchStudent(Request $request)
    {
        $search = User::with('department', 'country')
            ->where('type', User::USER_TYPE_STUDENT);

        if (!empty($request->employeeId)) {
            $search = $search->where('employee_id', 'like', "%$request->employeeId%");
        }

        if (!empty($request->wfmId)) {
            $search = $search->where('wfm_id', 'like', "%$request->wfmId%");
        }

        if (!empty($request->country)) {
            $search = $search->where('fk_country_id', $request->country);
        }

        if (!empty($request->department)) {
            $search = $search->where('fk_department_id', $request->department);
        }

        if (!empty($request->name)) {
            $search = $search->where('name', 'like', "%{$request->name}%");
        }

        return view('user.search-student', [
            'search' => $search->paginate()
        ]);

    }

    private function deleteStudent(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $user = User::findOrFail($request->id);
        if ($user->type != User::USER_TYPE_STUDENT) {
            abort(404);
        }

        $user->delete();

        return Response::json([
            'message' => "success"
        ]);
    }

    public function downloadStudentTemplate()
    {
        AuthHelper::hasPermissionElseAbort(Permission::BULK_UPLOAD_STUDENT_PERMISSION);
        return Storage::disk('oss')->response(self::UPLOAD_USER_TEMPLATE_PATH);
    }

    public function searchAjax($type, Request $request)
    {
        $type = strtoupper($type);

        switch ($type) {
            case User::USER_TYPE_STUDENT :
            {
                AuthHelper::hasPermissionElseAbort(Permission::SEARCH_STUDENT_PERMISSION);
                return $this->searchAjaxStudent($request);
                break;
            }
            default :
            {
                abort(404);
            }
        }
    }

    private function searchAjaxStudent(Request $request)
    {
        $courseId = $request->courseId;
        $searchText = $request->q;

        $request->validate([
            'courseId' => 'required|exists:courses,id',
            'q' => 'required|string|min:3'
        ]);

        $search = User::select('id', 'name')
            ->where('type', User::USER_TYPE_STUDENT);

        $userArray = DB::table('course_user')->select('fk_user_id')
            ->where('fk_course_id', $courseId)->get()->toArray();
        if (!empty($userArray)) {
            $search = $search->whereNotIn('id', array_column($userArray, 'fk_user_id'));
        }

        $search->where(function ($query) use ($searchText) {
            if (!empty($searchText)) {
                $query = $query->where('name', 'like', "%{$searchText}%")
                    ->orWhere('first_name', 'like', "%{$searchText}%")
                    ->orWhere('last_name', 'like', "%{$searchText}%")
                    ->orWhere('email', 'like', "%{$searchText}%")
                    ->orWhere('employee_id', 'like', "%$searchText%")
                ;
            }
            return $query;
        });

        return Response::json([
            'items' => $search->get()
        ]);

    }

    public function resetPasswordUser($id, Request $request)
    {
        $user = User::findOrFail($id);
        if ($user->type != User::USER_TYPE_STUDENT) {
            abort(404);
        }

        $request->validate([
            'password' => 'required|min:8|regex:/^[A-Za-z0-9_@.\#&+-]*$/i'
        ]);

        $user->password = bcrypt($request->password);
        $user->save();

        return Response::json([
            'message' => "Password updated successfully"
        ]);
    }
}
