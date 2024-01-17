<?php

namespace App\Http\Controllers\Course;

use App\Helpers\Carbon;
use App\Helpers\HtmlContentHelper;
use App\Helpers\OssStorageHelper;
use App\Helpers\PathHelper;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CourseViewController extends Controller
{
    public function index($categoryId = null, Request $request)
    {
        switch (Auth::user()->type) {
            case User::USER_TYPE_STUDENT:
            {
                return $this->studentIndex($categoryId, $request);
            }
            case User::USER_TYPE_ADMIN:
            {
                return $this->adminIndex($categoryId, $request);
            }
        }
    }

    public function studentIndex($categoryId, $request)
    {
        $course = Course::join('course_user', 'course_user.fk_course_id', '=', 'courses.id')->where([
            'is_active' => true,
            'course_user.fk_user_id' => Auth::id()
        ]);
        if (!empty($categoryId)) {
            $course = $course->where('fk_course_categories_id', $categoryId);
        }
        if ($request->has('search')) {
            $course = $course->where('name', 'LIKE', "%{$request->input('search')}%");
        }
        return view('course.index-student', ['courses' => $course->get()]);
    }

    public function adminIndex($categoryId, $request)
    {
        $course = Course::where(['is_active' => true, 'fk_course_categories_id' => $categoryId]);
        if ($request->has('search')) {
            $course = $course->where('name', 'LIKE', "%{$request->input('search')}%");
        }
        return view('course.index-admin', ['courses' => $course->get()]);
    }

    public function view($courseId)
    {
        $course = Course::findorfail($courseId);
        if (empty($course->content) || !$course->is_active) {
            abort(404);
        }
        if (Auth::user()->isStudent()) {
            $course->load([
                'users' => function ($query) {
                    $query->where('fk_user_id', Auth::id());
                }
            ]);
            if ($course->users->isEmpty()) {
                abort(404);
            }
        }
        DB::table('course_user')
            ->where(['fk_course_id' => $courseId, 'fk_user_id' => Auth::id()])
            ->update(array('last_access_at' => Carbon::now()));
        $htmlContent = !empty($course->content->path) ? Storage::get($course->content->path) : '';
        $htmlContent = HtmlContentHelper::getHtmlContent($htmlContent);

        $attachments = null;
        foreach ($course->content->attachments as $attachment) {
            if (PathHelper::isDocument($attachment->path)) {
                $pathInfo = PathHelper::getPathInfoForSecureUrl($attachment->path);
                $attachments[] = [
                    'id' => $attachment->id,
                    'ext' => PathHelper::getExtension($attachment->path),
                    'path' => OssStorageHelper::getSignUrl(PathHelper::getStoragePath() . $pathInfo['dirname'] . $pathInfo['basename'],
                        60),
                    'name' => $pathInfo['basename']
                ];
            }
        }

        $quizzes = Quiz::with('attempt')
            ->where(['fk_course_id' => $courseId, 'is_active' => true])->get();

        $arr = [];
        foreach ($quizzes as $key=>$quiz){
            $quizResult = $this->quizResults($quiz->id);
            $foo = new \StdClass();
            $foo->quiz_id = $quiz->id;
            $foo->quizResult = $quizResult;
            $arr[] = $foo;
        }


        $quizzes = $quizzes->map(function ($quizze) use ($arr){
            $quiz_id =  $quizze->id;
            $quiz = collect($arr)->where('quiz_id', $quiz_id)->first();
            $quizze->quiz = $quiz;
            return $quizze;
        });

        return view('course.view', [
            "course" => $course,
            "htmlContent" => $htmlContent,
            "attachments" => $attachments,
            "quizzes" => $quizzes
        ]);
    }


    //This function shows the result in quiz attempt page
    public function quizResults($quizId)
    {
        $quiz = DB::table('quizzes')
            ->join('quiz_attempts', 'quizzes.id', '=', 'quiz_attempts.fk_quiz_id')
            ->join('quiz_attempts_details', 'quiz_attempts.id', '=', 'quiz_attempts_details.fk_quiz_attempt_id')
            ->select('quizzes.attempts_allowed as total','quizzes.fk_course_id as courseId',
                'quiz_attempts.total_attempts as attempts', 'quiz_attempts.grading_final_result as grade',
                'quiz_attempts.grading_percentage as grade_percentage','quiz_attempts_details.quiz_attempt', 'quiz_attempts_details.quiz_given')
            ->where('quiz_attempts.fk_user_id', Auth::id())
            ->where('quizzes.id', $quizId)
            ->get()->toArray();


        $results = [];
        if(!empty($quiz)){
            $lastQuiz = end($quiz);
            if ($lastQuiz->quiz_attempt!='[]'){
                $quiz_attempt = json_decode( $lastQuiz->quiz_attempt, true);
                $quiz_given = json_decode( $lastQuiz->quiz_given, true);
                $questionName = QuizQuestion::select(DB::raw('group_concat(name) as names'))
                    ->where('fk_quiz_id',$quizId)->pluck('names');
                $questionName = explode(',',  $questionName[0]);
                foreach ($quiz_given as $index => $question) {
                    $attempted_options = $quiz_attempt[$index];

                    $correct_options = array_filter($question["options"], function($option) {
                        return $option["isCorrect"] == true;
                    });
                    $result = "Incorrect";
                    if (count($attempted_options) == count($correct_options)) {
                        sort($attempted_options);
                        $keys = array_keys($correct_options);
                        sort($keys);
                        if ($attempted_options == $keys) {
                            $result = "Correct";
                        }
                    }
                    if(!empty($questionName[$index])){
                        $results[] = [
                            "question_name" => $questionName[$index],
                            "result" => $result,
                            "attempted_options" => $attempted_options,
                            "correct_options" => array_keys($correct_options),
                        ];
                    }
                }
            }
        }
        return $results;




    }



}
