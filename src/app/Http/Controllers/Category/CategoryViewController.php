<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryViewController extends Controller
{
    public function index(Request $request)
    {
        switch (Auth::user()->type) {
            case User::USER_TYPE_STUDENT:
            {
                return $this->studentIndex($request);
            }
            case User::USER_TYPE_ADMIN:
            {
                return $this->adminIndex($request);
            }
        }
    }

    public function studentIndex($request)
    {
        $categories = DB::table('course_categories')
            ->leftJoin('courses', function ($join) {
                $join->on('course_categories.id', '=', 'courses.fk_course_categories_id');
                $join->where('courses.is_active', true);
            })
            ->leftJoin('course_user', 'courses.id', '=', 'course_user.fk_course_id')
            ->leftJoin('users', 'course_user.fk_user_id', '=', 'users.id')
            ->where('course_categories.is_active', true)
            ->where('course_categories.deleted_at', null)
            ->where('course_categories.name', 'LIKE', "%{$request->input('search')}%")
            ->where(function ($query) {
                $query->where('users.id', Auth::id());
                $query->orWhereNull('users.id');
            })->groupBy('course_categories.id')
            ->select(
                'course_categories.id',
                DB::raw('min(users.id) as user_id'),
                DB::raw('min(course_categories.name) as name'),
                DB::raw('min(course_categories.image_path) as image_path'),
                DB::raw('min(course_categories.image_name) as image_name')
            )
            ->get();

        return view('category.index-student', ['categories' => $categories]);
    }

    public function adminIndex(Request $request)
    {
        $allCategory = CourseCategory::where('is_active', true);

        if ($request->has('search')) {
            $allCategory = $allCategory->where('name', 'LIKE', "%{$request->input('search')}%");
        }
        return view('category.index-admin', ['categories' => $allCategory->get()]);
    }
}
