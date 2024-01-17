<?php

namespace App\Http\Controllers\Course;

use App\Helpers\AuthHelper;
use App\Helpers\OssStorageHelper;
use App\Helpers\QueueHelper;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class CourseParticipantController extends Controller
{
    const UPLOAD_PARTICIPANT_TEMPLATE_PATH = 'template/UploadParticipantTemplate.csv';

    public function search($courseId, Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::SEARCH_COURSE_PARTICIPANT_PERMISSION);
        Course::findOrFail($courseId);

        $search = User::select('users.*', 'countries.name as venture', 'departments.name as department',
            'course_user.last_access_at as lastAccess')
            ->join('course_user', 'users.id', '=', 'course_user.fk_user_id')
            ->join('countries', 'users.fk_country_id', '=', 'countries.id')
            ->join('departments', 'users.fk_department_id', '=', 'departments.id')
            ->where('fk_course_id', $courseId);

        if (!empty($request->employeeId)) {
            $search = $search->where('users.employee_id', 'like', "%$request->employeeId%");
        }

        if (!empty($request->wfmId)) {
            $search = $search->where('users.wfm_id', 'like', "%$request->wfmId%");
        }

        if (!empty($request->country)) {
            $search = $search->where('fk_country_id', $request->country);
        }

        if (!empty($request->department)) {
            $search = $search->where('fk_department_id', $request->department);
        }

        if (!empty($request->name)) {
            $search = $search->where('users.name', 'like', "%{$request->name}%");
        }

        return view('course.search-participant', [
            'search' => $search->paginate()
        ]);
    }

    public function enroll($courseId, Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::ENROLL_COURSE_PARTICIPANT_PERMISSION);
        $course = Course::findOrFail($courseId);

        $userId = $request->userId;
        $user = User::findOrFail($userId);
        try {
            $course->users()->attach($user);
            return Response::json([
                'message' => "success"
            ]);
        } catch (\Exception $ex) {
            return Response::json([
                'message' => "fail"
            ], 400);
        }
    }

    public function remove($courseId, Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::REMOVE_COURSE_PARTICIPANT_PERMISSION);
        $course = Course::findOrFail($courseId);

        $userId = $request->userId;
        $user = User::findOrFail($userId);
        if (!$course->users()->detach($user)) {
            return Response::json([
                'message' => "fail"
            ], 400);
        }

        return Response::json([
            'message' => "success"
        ]);
    }

    public function bulkUploadCourseParticipant($courseId, Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::BULK_UPLOAD_COURSE_PARTICIPANT_PERMISSION);
        Course::findOrFail($courseId);

        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048'
        ]);
        $filePath = OssStorageHelper::storeUploadedFileToTemp($request->file('file'));
        Artisan::queue('import:course-participant',
            [
                'filePath' => $filePath['path'] . $filePath['name'],
                'userId' => Auth::id(),
                'courseId' => $courseId
            ]
        )->onQueue(QueueHelper::IMPORT_COURSE_PARTICIPANT_QUEUE);

        return back()->with('status.success', 'File has uploaded sucessfully');
    }

    public function downloadParticipantTemplate()
    {
        AuthHelper::hasPermissionElseAbort(Permission::BULK_UPLOAD_COURSE_PARTICIPANT_PERMISSION);
        return Storage::disk('oss')->response(self::UPLOAD_PARTICIPANT_TEMPLATE_PATH);
    }
}
