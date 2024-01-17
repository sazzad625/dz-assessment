<?php

namespace App\Http\Controllers\Report;

use App\Helpers\AuthHelper;
use App\Helpers\OssStorageHelper;
use App\Helpers\PathHelper;
use App\Helpers\QueueHelper;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\ReportExport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Excel;


class IndividualPerformanceReportController extends Controller
{
    public function index(Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::INDIVIDUAL_PERFORMANCE_REPORT_PERMISSION);
        $search = User::on('mysql_readonly')->with('department', 'country')
            ->where('type', User::USER_TYPE_STUDENT);

        if (!empty($request->country)) {
            $search = $search->where('fk_country_id', $request->country);
        }

        if (!empty($request->department)) {
            $search = $search->where('fk_department_id', $request->department);
        }

        if (!empty($request->name)) {
            $search = $search->where('name', 'like', "%{$request->name}%");
        }

        if (!empty($request->email)) {
            $search = $search->where('email', 'like', "%{$request->email}%");
        }

        if (!empty($request->employeeId)) {
            $search = $search->where('employee_id', 'like', "%{$request->employeeId}%");
        }

        $list = ReportExport::where(['fk_user_id' => Auth::id(),
            'type' => ReportExport::REPORT_TYPE_INDIVIDUAL_PERFORMANCE])->take(5)->orderby('id', 'DESC')->get();
        return view('report.individual-performance', [
            'search' => $search->paginate(),
            'downloadFiles' => $list
        ]);
    }

    public function student($id)
    {
        AuthHelper::hasPermissionElseAbort(Permission::INDIVIDUAL_PERFORMANCE_REPORT_PERMISSION);

        $user = User::on('mysql_readonly')
            ->select('first_name', 'last_name', 'users.name', 'courses.name AS courses',
                DB::raw('min(courses.id) AS course_id'),
                DB::raw('min(users.id) AS user_id'),
                DB::raw('sum(quiz_attempts.grading_percentage) AS percentage'),
                DB::raw('count(quiz_attempts.id) AS attempted'),
                DB::raw('count(quizzes.id) AS totalQuizzes'))
            ->join('course_user', 'users.id', '=', 'course_user.fk_user_id')
            ->join('courses', 'courses.id', '=', 'course_user.fk_course_id')
            ->leftJoin('quizzes', 'courses.id', '=', 'quizzes.fk_course_id')
            ->leftJoin('quiz_attempts', function ($join) {
                $join->on('quizzes.id', '=', 'quiz_attempts.fk_quiz_id');
                $join->on('quiz_attempts.fk_user_id', '=', 'users.id');
            })
            ->where('users.id', $id)
            ->whereNull('quizzes.deleted_at')
            ->groupBy('course_user.fk_user_id', 'first_name', 'last_name', 'users.name', 'courses.name')
            ->get()->toArray();

        foreach ($user as $key => $value) {
            $user[$key]['percentage'] = !empty($value['percentage']) && !empty($value['totalQuizzes'])
                ? $value['percentage'] / $value['totalQuizzes'] : 0;

            if ($value['totalQuizzes'] == 0) {
                $user[$key]['status'] = "No Quiz Found";
            }  else if ($value['attempted'] == 0) {
                $user[$key]['status'] = "Not Started";
            } else if ($value['attempted'] == $value['totalQuizzes']) {
                $user[$key]['status'] = "Completed";
            } else {
                $user[$key]['status'] = "In Progress";
            }
        }


        return view('report.individual-student-performance',
            [
                'user' => $user,
            ]);
    }

    public function generateFile(Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::INDIVIDUAL_PERFORMANCE_REPORT_DOWNLOAD_PERMISSION);

        if (empty($request->id) ||
            count(User::on('mysql_readonly')->whereIn('id', $request->id)
                ->where('type', 'STUDENT')->get()) != count($request->id)) {
            abort(404);
        }
        $lastRecord = ReportExport::where(['fk_user_id' => Auth::id(),
            'type' => ReportExport::REPORT_TYPE_INDIVIDUAL_PERFORMANCE])->orderby('id', 'DESC')->first();
        if (!empty($lastRecord) && $lastRecord->status == ReportExport::STATUS_QUEUED) {
            return Response::json([
                'message' => "Your request is in pending. You can not create another request"
            ]);
        }

        $reportExport = new ReportExport();
        $reportExport->type = ReportExport::REPORT_TYPE_INDIVIDUAL_PERFORMANCE;
        $reportExport->status = ReportExport::STATUS_QUEUED;
        $reportExport->fk_user_id = Auth::id();
        $reportExport->save();

        Artisan::queue('export:individual-performance-report',
            [
                'studentIds' => $request->id,
                'reportExportId' => $reportExport->id
            ]
        )->onQueue(QueueHelper::EXPORT_INDIVIDUAL_PERFORMANCE_REPORT_QUEUE);

        return Response::json([
            'message' => "Request Created Successfully"
        ]);
    }

    public function generateFileForAllRecords(Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::INDIVIDUAL_PERFORMANCE_REPORT_DOWNLOAD_PERMISSION);

        $lastRecord = ReportExport::where(['fk_user_id' => Auth::id(),
            'type' => ReportExport::REPORT_TYPE_INDIVIDUAL_PERFORMANCE])->orderby('id', 'DESC')->first();
        if (!empty($lastRecord) && $lastRecord->status == ReportExport::STATUS_QUEUED) {
            return Response::json([
                'message' => "Your request is in pending. You can not create another request"
            ]);
        }

        $search = User::on('mysql_readonly')->with('department', 'country')
            ->where('type', User::USER_TYPE_STUDENT);

        if (!empty($request->country)) {
            $search = $search->where('fk_country_id', $request->country);
        }

        if (!empty($request->department)) {
            $search = $search->where('fk_department_id', $request->department);
        }

        if (!empty($request->name)) {
            $search = $search->where('name', 'like', "%{$request->name}%");
        }

        if (!empty($request->employeeId)) {
            $search = $search->where('employee_id', 'like', "%{$request->employeeId}%");
        }
        $userIds = array_column($search->get()->toArray(), 'id');

        $reportExport = new ReportExport();
        $reportExport->type = ReportExport::REPORT_TYPE_INDIVIDUAL_PERFORMANCE;
        $reportExport->status = ReportExport::STATUS_QUEUED;
        $reportExport->fk_user_id = Auth::id();
        $reportExport->save();

        Artisan::queue('export:individual-performance-report',
            [
                'studentIds' => $userIds,
                'reportExportId' => $reportExport->id
            ]
        )->onQueue(QueueHelper::EXPORT_INDIVIDUAL_PERFORMANCE_REPORT_QUEUE);

        return Response::json([
            'message' => "Request Created Successfully"
        ]);
    }

    public function getGenerateFiles()
    {
        AuthHelper::hasPermissionElseAbort(Permission::INDIVIDUAL_PERFORMANCE_REPORT_DOWNLOAD_PERMISSION);

        $list = ReportExport::where(['fk_user_id' => Auth::id(),
            'type' => ReportExport::REPORT_TYPE_INDIVIDUAL_PERFORMANCE])->take(5)->orderby('id', 'DESC')->get();

        return view('layouts.partials.download-list',
            [
                'list' => $list,
                'downloadRoute' => route('report.course-performance.download', ''),
            ]);
    }

    public function download($path)
    {
        AuthHelper::hasPermissionElseAbort(Permission::INDIVIDUAL_PERFORMANCE_REPORT_DOWNLOAD_PERMISSION);
        return Storage::disk('oss')->response($path);
    }
}
