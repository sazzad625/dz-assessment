<?php

namespace App\Http\Controllers\Report;

use App\Helpers\AuthHelper;
use App\Helpers\QueueHelper;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Permission;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\ReportExport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TotalAttemptsExport;

class CoursePerformanceReportController extends Controller
{
    public function index($courseId = null, Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::COURSE_PERFORMANCE_REPORT_PERMISSION);

        // '/report/course-performance-report/' route is not needed

//        if (empty($courseId)) {
//            return view('report.course-performance');
//        }

        $search = Course::on('mysql_readonly')
            ->select('users.id AS id', 'first_name', 'last_name', 'users.name AS userName', 'users.employee_id',
                'users.email', 'courses.name AS courses', 'departments.name AS department', 'countries.name AS venture')
            ->join('course_user', 'course_user.fk_course_id', '=', 'courses.id')
            ->join('users', 'users.id', '=', 'course_user.fk_user_id')
            ->join('departments', 'departments.id', '=', 'users.fk_department_id')
            ->join('countries', 'countries.id', '=', 'users.fk_country_id')
            ->where('courses.id', $courseId);

        $filter = '';

        if (!empty($request->name)) {
            $search = $search->where('users.name', 'like', "%{$request->name}%");
            $userName = DB::table('users')->where('name', $request->name)->first();
            if(!empty($userName)){
                if(!empty($filter)){
                    $filter = $filter. ', ' . $userName->name;
                }else{
                    $filter = $userName->name;
                }
            }
        }else if(empty($request->name)){
            $filter = 'NIL';
        }

        if (!empty($request->employeeId)) {
            $search = $search->where('users.employee_id', 'like', "%{$request->employeeId}%");

            $userId = DB::table('users')->where('employee_id', $request->employeeId)->first();
            if(!empty($userId)){
                if(!empty($filter)){
                    $filter = $filter. ', ' . $userId->employee_id;
                }else{
                    $filter = $userId->employee_id;
                }
            }
        }else if(empty($request->employeeId)){
            $filter = $filter.', NIL';
        }

        if (!empty($request->country)) {
            $search = $search->where('users.fk_country_id', $request->country);
            $countryName = DB::table('countries')->where('id', $request->country)->first()->name;
            if(!empty($filter)){
                $filter = $filter. ', ' . $countryName;
            }else{
                $filter = $countryName;
            }
        }else if(empty($request->country)){
            $filter = $filter.', NIL';
        }

        if (!empty($request->department)){
            $search = $search->where('users.fk_department_id', $request->department);
            $departmentName = DB::table('departments')->where('id', $request->department)->first()->name;
            if(!empty($filter)){
                $filter = $filter. ', ' . $departmentName;
            }else{
                $filter = $departmentName;
            }
        }else if(empty($request->department)){
            $filter = $filter.', NIL';

        }


        if (!empty($request->email)) {
            $search = $search->where('users.email', 'like', "%{$request->email}%");
        }




        $search = $search->simplePaginate();


        $data = !empty($search) ? $search->toArray()['data'] : [];

        $quizzesColumn = Quiz::with('attempts')
            ->where('fk_course_id', $courseId)->orderBy('quizzes.id')->get();



        $array = [];
        foreach ($data as $value) {
                $array[$value['id']] = $value;
        }
        foreach ($quizzesColumn as $quiz) {
            foreach ($quiz->attempts as $value) {
                    if (!empty($array[$value->fk_user_id])){
                        $array[$value->fk_user_id][$quiz->name] = $value->grading_percentage;
                        $array[$value->fk_user_id]['totalPercentage'] =
                            !empty($array[$value->fk_user_id]['totalPercentage']) ?
                                $array[$value->fk_user_id]['totalPercentage'] + $value->grading_percentage :
                                $value->grading_percentage;
                    }
                }
        }

        $list = ReportExport::where(['fk_user_id' => Auth::id(),
            'type' => ReportExport::REPORT_TYPE_COURSE_PERFORMANCE])->take(5)->orderby('id', 'DESC')->get();

        // Calculating the quiz performance average

        $quizPercentageAvg = QuizAttempt::selectRaw('ROUND(sum(quiz_attempts.grading_percentage) / count(quiz_attempts.id), 2) as avg_grade,
         sum(quiz_attempts.grading_percentage) as sum, count(quiz_attempts.id) as count,
         sum(quiz_attempts.total_attempts) as total_attempts,
            ROUND(sum(quiz_attempts.total_attempts) / count(quiz_attempts.id), 2) as avg_attempt, quiz_attempts.fk_quiz_id')
            ->join('quizzes','quizzes.id','=','quiz_attempts.fk_quiz_id')
            ->where('quizzes.fk_course_id', $courseId)
            ->where('quizzes.is_active', 1)
            ->where('quiz_attempts.grading_final_result','PASS')
            ->groupBy('quiz_attempts.fk_quiz_id')
            ->get();

        return view('report.course-performance', [
            'pagination' => $search,
            'columns' => $quizzesColumn,
            'search' => $array,
            'downloadFiles' => $list,
            'filter' => $filter,
            'data' => $data,
            'quizPercentageAvg' =>  $quizPercentageAvg
        ]);
    }

    public function optionsCount($courseId, $quizzId){

        $quizAttempts = DB::table('quizzes')
            ->select('quiz_attempts_details.quiz_given','quiz_attempts_details.quiz_attempt')
            ->where('quizzes.fk_course_id', $courseId)
            ->join('quiz_attempts','quiz_attempts.fk_quiz_id','=','quizzes.id')
            ->join('quiz_attempts_details','quiz_attempts_details.fk_quiz_attempt_id','=','quiz_attempts.id')
            ->where('quiz_attempts.fk_quiz_id',$quizzId)
            ->get();

        $questionName = QuizQuestion::withTrashed()
            ->select(DB::raw('group_concat(name) as names'))
            ->where('fk_quiz_id', $quizzId)
            ->pluck('names');

        $questionsName = explode(',', $questionName[0]);

        // Initialize an empty array to store the option counts
        $questionOptionCounts = [];
        $correct_options = [];

        // Loop through each quiz attempt
        foreach ($quizAttempts as $attempt) {
            // Decode the quiz_given JSON string to an array
            $questions = json_decode($attempt->quiz_given, true);


            foreach ($questions as $key => $question) {
                $options = $question["options"];
                $correct_options[$key] = array_column(array_filter($options, function($option) { return $option["isCorrect"]; }), 'text');
            }

            // Decode the quiz_attempt JSON string to an array
            $selectedOptions = json_decode($attempt->quiz_attempt, true);
            // Loop through each question and count the number of options selected by the user
            foreach ($questions as $index => $question) {
                $options = $question['options'];
                if(!empty($selectedOptions[$index])){
                    $selected = $selectedOptions[$index];
                    // Increment the count for each selected option for this question
                    foreach ($selected as $optionIndex) {
                        $optionName = $options[$optionIndex]['text'];
                        if (!isset($questionOptionCounts[$index][$optionName])) {
                            $questionOptionCounts[$index][$optionName] = 0;
                        }
                        $questionOptionCounts[$index][$optionName]++;
                    }
                }
            }
        }

        // Output the question option counts
//        $results = [];
//
//        foreach ($questionOptionCounts as $key => $questionOptionCount) {
//
//            $total = array_sum($questionOptionCount);
//            foreach ($questionOptionCount as $option_text => $count) {
//                $percentage = round(($count / $total) * 100, 2);
//                $questionOptionCount[$option_text]  = $percentage . "%";
//            }
//            array_push($results,$questionsName[$key],$questionOptionCount);
//        }

        $results = [];

        foreach ($questionOptionCounts as $key => $questionOptionCount) {
            $total = array_sum($questionOptionCount);
            foreach ($questionOptionCount as $option_text => $count) {
                $percentage = round(($count / $total) * 100, 2);
                $questionOptionCount[$option_text]  = $percentage . "%";
            }
            $results[] = [
                'question_name' => $questionsName[$key],
                'option_counts' => $questionOptionCount
            ];
        }


        // sort the results by question_name
//        usort($results, function($a, $b) {
//            return strcmp($a['question_name'], $b['question_name']);
//        });

        return [$results,$correct_options];

    }

    public function quizTotalAttempts($courseId, $quizzId){

            // Collecting attempts details for that quiz
            $quizAttempts = DB::table('quizzes')
                ->where('quizzes.fk_course_id', $courseId)
                ->join('quiz_attempts','quiz_attempts.fk_quiz_id','=','quizzes.id')
                ->join('quiz_attempts_details','quiz_attempts_details.fk_quiz_attempt_id','=','quiz_attempts.id')
                ->where('quiz_attempts.fk_quiz_id',$quizzId)
                ->get();

        $questionName = QuizQuestion::withTrashed()
            ->select(DB::raw('group_concat(name) as names'))
            ->where('fk_quiz_id', $quizzId)
            ->pluck('names');

        $questions = explode(',', $questionName[0]);

        $totalAttempts = [];

            foreach ($quizAttempts as $quizAttempt){
                $quiz_attempt = json_decode( $quizAttempt->quiz_attempt, true);
                $quiz_given = json_decode( $quizAttempt->quiz_given, true);

                foreach ($quiz_given as $index => $question) {
                    if (!isset($totalAttempts[$index])) {
                        $totalAttempts[$index] = [
                            'total_attempts' => 0,
                            'correct_attempts' => 0,
                            'incorrect_attempts' => 0,
                        ];
                    }

                    if (!empty($quiz_attempt[$index])){
                        $attempted_options = $quiz_attempt[$index];
                        $correct_options = array_filter($question["options"], function($option) {
                            return $option["isCorrect"] == true;
                        });
                        if (count($attempted_options) == count($correct_options)) {
                            sort($attempted_options);
                            $keys = array_keys($correct_options);
                            sort($keys);
                            if ($attempted_options == $keys) {
                                $totalAttempts[$index]['correct_attempts']++;
                            }else{
                                $totalAttempts[$index]['incorrect_attempts']++;
                            }
                        }else{
                            $totalAttempts[$index]['incorrect_attempts']++;
                        }
                        $totalAttempts[$index]['total_attempts']++;
                    }
                    }
            }

            return [$questions,$totalAttempts];
    }

    public function generateFile(Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::COURSE_PERFORMANCE_REPORT_EXPORT_PERMISSION);

        $lastRecord = ReportExport::where(['fk_user_id' => Auth::id(),
            'type' => ReportExport::REPORT_TYPE_COURSE_PERFORMANCE])->orderby('id', 'DESC')->first();
        if (!empty($lastRecord) &&
            ($lastRecord->status == ReportExport::STATUS_QUEUED || $lastRecord->status == ReportExport::STATUS_IN_PROGRESS)
        ){
            return Response::json([
                'message' => "Your request is in pending. You can not create another request"
            ]);
        }

        $reportExport = new ReportExport();
        $reportExport->type = ReportExport::REPORT_TYPE_COURSE_PERFORMANCE;
        $reportExport->status = ReportExport::STATUS_QUEUED;
        $reportExport->fk_user_id = Auth::id();
        $reportExport->filters_used = $request->filters_used;
        $reportExport->save();

        Artisan::queue('export:course-performance-report',
            [
                'studentIds' => $request->ids,
                'courseId' => $request->courseId,
                'reportExportId' => $reportExport->id
            ]
        )->onQueue(QueueHelper::EXPORT_COURSE_PERFORMANCE_REPORT_QUEUE);

        return Response::json([
            'message' => "Request Created Successfully"
        ]);
    }

    public function generateFileForAllRecords(Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::COURSE_PERFORMANCE_REPORT_EXPORT_PERMISSION);

        $lastRecord = ReportExport::where(['fk_user_id' => Auth::id(),
            'type' => ReportExport::REPORT_TYPE_COURSE_PERFORMANCE])->orderby('id', 'DESC')->first();
        if (!empty($lastRecord) &&
            ($lastRecord->status == ReportExport::STATUS_QUEUED || $lastRecord->status == ReportExport::STATUS_IN_PROGRESS)
        ){
            return Response::json([
                'message' => "Your request is in pending. You can not create another request"
            ]);
        }

        if (empty($request->courseId)) {
            return Response::json([
                'message' => "course Id not found"
            ], 400);
        }

        $search = Course::on('mysql_readonly')
        ->select('users.id AS id')
        ->join('course_user', 'course_user.fk_course_id', '=', 'courses.id')
        ->join('users', 'users.id', '=', 'course_user.fk_user_id')
        ->where('courses.id', $request->courseId);

        if (!empty($request->country)) {
            $search = $search->join('countries', 'countries.id', '=', 'users.fk_country_id')
                        ->where('users.fk_country_id', $request->country);
        }

        if (!empty($request->department)) {
            $search = $search->join('departments', 'departments.id', '=', 'users.fk_department_id')
                        ->where('users.fk_department_id', $request->department);
        }

        if (!empty($request->name)) {
            $search = $search->where('users.name', 'like', "%{$request->name}%");
        }

        if (!empty($request->employeeId)) {
            $search = $search->where('users.employee_id', 'like', "%{$request->employeeId}%");
        }

        $userIds = array_column($search->get()->toArray(), 'id');

        $reportExport = new ReportExport();
        $reportExport->type = ReportExport::REPORT_TYPE_COURSE_PERFORMANCE;
        $reportExport->status = ReportExport::STATUS_QUEUED;
        $reportExport->fk_user_id = Auth::id();
        $reportExport->filters_used = $request->filters_used;
        $reportExport->save();

        Artisan::queue('export:course-performance-report',
            [
                'studentIds' =>  $userIds,
                'courseId' => $request->courseId,
                'reportExportId' => $reportExport->id
            ]
        )->onQueue(QueueHelper::EXPORT_COURSE_PERFORMANCE_REPORT_QUEUE);

        return Response::json([
            'message' => "Request Created Successfully"
        ]);
    }

    public function getGeneratedFilesList()
    {
        AuthHelper::hasPermissionElseAbort(Permission::COURSE_PERFORMANCE_REPORT_EXPORT_PERMISSION);
        $list = ReportExport::where(['fk_user_id' => Auth::id(),
            'type' => ReportExport::REPORT_TYPE_COURSE_PERFORMANCE])->take(5)->orderby('id', 'DESC')->get();

        return view('layouts.partials.download-list',
            [
                'list' => $list,
                'downloadRoute' => route('report.course-performance.download', ''),
            ]);
    }
    public function export($courseId,$quiz_id)
    {
        $var = $this->quizTotalAttempts($courseId, $quiz_id);
        $questions = $var[0];
        $totalAttempts = $var[1];
        return Excel::download(new TotalAttemptsExport($totalAttempts, $questions), 'total_attempts.xlsx');
    }

    public function quizAnalytics($courseId=null,$quiz_id=null) {

        //Quiz performance report
        $totalAttempts =[];
        $questions =[];
        $optionsCounts=[];
        $correctOptions=[];

        //It will return the report data
        if (!empty($quiz_id)){
            $var = $this->quizTotalAttempts($courseId, $quiz_id);
            $optionsCountsMerged = $this->optionsCount($courseId, $quiz_id);
            $optionsCounts = $optionsCountsMerged[0];
            $correctOptions = $optionsCountsMerged[1];
            $questions = $var[0];
            $totalAttempts = $var[1];
        }

        return view('layouts.partials.quiz-list',
            [
                'totalAttempts' => $totalAttempts,
                'questions' => $questions,
                'optionsCounts' => $optionsCounts,
                'correctOptions' => $correctOptions,
                'courseId' => $courseId,
                'quiz_id' => $quiz_id
            ]);
    }

}
