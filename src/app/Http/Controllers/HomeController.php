<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        switch (Auth::user()->type) {
            case User::USER_TYPE_TEACHER:
            case User::USER_TYPE_ADMIN:
            {
                return $this->adminDashboard();
            }
            case User::USER_TYPE_STUDENT:
            {
                return redirect()->route('category');
            }
        }
    }

    private function adminDashboard()
    {
        return view('admin-dashboard');
    }

    public function getCourseCount()
    {
        AuthHelper::ifStudentThenAbort();
        $count = Course::on('mysql_readonly')->where('is_active', 1)->count();

        return [
            'count' => $count
        ];
    }

    public function getCourseCategoryCount()
    {
        AuthHelper::ifStudentThenAbort();
        $count = CourseCategory::on('mysql_readonly')->where('is_active', 1)->count();

        return [
            'count' => $count
        ];
    }

    public function getStudentCount()
    {
        AuthHelper::ifStudentThenAbort();
        $studentsCount = User::on('mysql_readonly')->select('departments.id', 'departments.name',
            DB::raw('count(*) AS count'))
            ->join('departments', 'users.fk_department_id', '=', 'departments.id')
            ->groupBy('departments.id', 'departments.name')->get();

        return [
            'data' => $studentsCount
        ];
    }

    public function getTeacherCount()
    {
        AuthHelper::ifStudentThenAbort();
        $count = User::on('mysql_readonly')->where('type', User::USER_TYPE_TEACHER)->count();

        return [
            'count' => $count
        ];
    }

    public function getAttemptedQuizCount()
    {
        AuthHelper::ifStudentThenAbort();
        $count = QuizAttempt::on('mysql_readonly')->count();

        return [
            'count' => $count
        ];
    }
}
