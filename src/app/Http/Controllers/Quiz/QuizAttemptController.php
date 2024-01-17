<?php

namespace App\Http\Controllers\Quiz;

use App\Helpers\AuthHelper;
use App\Helpers\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptDetail;
use App\Models\QuizQuestion;
use App\Quiz\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QuizAttemptController extends Controller
{

    public function initiate($quizId)
    {
        //Only student is allowed to attempt
        AuthHelper::isStudentElseAbort();

        $quiz = Quiz::findOrFail($quizId); //if quiz is not found then return error

        //prevent quiz attempt if it is inactive.
        if (!$quiz->is_active) {
            return response([
                'message' => "Bad request"
            ], 400);
        }

        //load related data
        $quiz->load([
            'course',
            'grading',
            'course.users' => function ($query) {
                $query->where('fk_user_id', Auth::id()); // only fetch current student data
            }
        ]);

        // if current user is not enrolled then return forbidden response
        if ($quiz->course->users->isEmpty()) {
            abort(403, 'You are not enrolled in this course');
        }

        //load quiz_attempts table related data
        $quiz->load('attempt');

        //if quiz_attempt record not exists then create it
        if ($quiz->attempt == null) {
            //if attempt insertion failed then return error response
            if (!$this->insertQuizAttemptRow($quiz)) {
                return response([
                    'message' => 'Unknown error occurred'
                ], 500);
            }
        }

        //prevent student to attempt this quiz if he have reached max allowed limit
        if ($quiz->attempt->total_attempts >= $quiz->attempts_allowed) {
            return response([
                'message' => 'You have reached your max attempt limit'
            ], 400);
        }

        //load previous pending attempts
        $quiz->load([
            'attempt.details' => function ($query) {
                // if expected end time is greater then current time then status should not be pending
                $query->where('status', QuizAttemptDetail::STATUS_INITIATED);
                $query->whereNotNull('expected_end_time');
                $query->where('expected_end_time', '>', Carbon::now());
            }
        ]);

        //if student have already pending quiz then redirect to it
        if (!$quiz->attempt->details->isEmpty()) {
            return [
                'message' => 'success',
                'link' => route('quiz.attempt', $quiz->attempt->details->last()->id)
            ];
        }

        $quizQuestions = QuizQuestion::getQuizQuestions($quizId);

        //if quiz does not contains any questions then return error response
        if (empty($quizQuestions)) {
            return response([
                'message' => 'No questions have been added to this quiz'
            ], 400);
        }

        $quizAttemptDetail = $this->insertQuizAttemptDetail($quiz, $quizQuestions);
        $quiz->attempt->last_attempt_time = Carbon::now();
        $quiz->attempt->total_attempts++;
        $quiz->attempt->save();


        return [
            'message' => 'success',
            'link' => route('quiz.attempt', $quizAttemptDetail->id)
        ];
    }

    /**
     * This is the centralized check for attempt validations
     * @param $attemptDetailId
     * @param int $increasedQuizTime time in minutes to allow question to still submit if expected end time is passed
     * @return QuizAttemptDetail
     */
    private function validateAttemptRequest($attemptDetailId, $increasedQuizTime = 0)
    {
        //Only student is allowed to attempt
        AuthHelper::isStudentElseAbort();

        $quizAttemptDetail = QuizAttemptDetail::findOrFail($attemptDetailId); //if no record found then return error
        $quizAttemptDetail->load('attempt', 'attempt.user'); //load quiz attempt and user data

        //prevent this operation if attempt does not belongs to current user
        if ($quizAttemptDetail->attempt->user->id != Auth::id()) {
            abort(404);
        }

        //prevent this operation if quiz is completed or expected end time have been passed.
        if ($quizAttemptDetail->status == QuizAttemptDetail::STATUS_COMPLETED || (
                //if expected end time is not given then its no limit quiz end time will not be checked
                !empty($quizAttemptDetail->expected_end_time)
                && $quizAttemptDetail->expected_end_time->addMinutes($increasedQuizTime)->lessThanOrEqualTo(Carbon::now())
            )) {
            abort(404);
        }

        return $quizAttemptDetail;
    }

    public function attempt($attemptDetailId)
    {
        $quizAttemptDetail = $this->validateAttemptRequest($attemptDetailId);


        $quizId = $quizAttemptDetail->attempt->fk_quiz_id;

        //contains quiz given to student

//      <div>{!! $questionsHeader !!}</div>
        $questionsArray = json_decode($quizAttemptDetail->quiz_given, true,JSON_UNESCAPED_UNICODE);

        //contains the answers given by student in case user refreshes the page his chosen answers will not be lost
        $answersArray = json_decode($quizAttemptDetail->quiz_attempt, true);

        //contains final questions json which will be sent to attempt, Excluding answers.
        $questions = [];

        foreach ($questionsArray as $index => $question) {
            $tempQuestion = Question::of($question);
            //if student given answer for this question then populate it
            if (array_key_exists($index, $answersArray)) {
                $tempQuestion->setAnswer($answersArray[$index]);
            }
            $questions[] = $tempQuestion->serializeExcludeAnswers(); //adding question to final array excluding answers
        }

        return view('quiz.attempt', [
            'attemptDetailId' => $attemptDetailId,
            'questions' => $questions,
            'quizId' => $quizId,
            'timeLimit' => $quizAttemptDetail->expected_end_time != null ?
                $quizAttemptDetail->expected_end_time->diffInSeconds(Carbon::now()) : 'null'
        ]);
    }

    /**
     * This method is used for both operations save state and submit depends on the mode passed
     * @param $attemptDetailId
     * @param Request $request
     * @return array
     * @throws \App\Quiz\exceptions\UnsupportedTypeException
     */
    public function submit($attemptDetailId, Request $request)
    {
        //validate request else return error response
        $request->validate([
            'answers' => 'required|json',
            'mode' => 'required|in:SAVE_STATE,SUBMIT'
        ]);

        //if validation fails code will not go to next line
        $quizAttemptDetail = $this->validateAttemptRequest($attemptDetailId, 5);
        $quizAttemptDetail->quiz_attempt = $request->answers;

        $quizAttemptDetail->saveCalculatedResult();

        //if mode is not submit then do not perform final calculations
        if ($request->mode != "SUBMIT") {
            return ['message' => 'success'];
        }

        $quizAttemptDetail->status = QuizAttemptDetail::STATUS_COMPLETED;
        $quizAttemptDetail->end_time = Carbon::now();
        $quizAttemptDetail->save();

        $quizAttemptDetail->attempt->saveAggregatedData();

        return ['message' => 'success'];

    }

    private function insertQuizAttemptRow($quiz)
    {
        $quizAttempt = new QuizAttempt();
        $quizAttempt->user()->associate(Auth::user());
        $quizAttempt->grading()->associate($quiz->grading);
        $quizAttempt->total_attempts = 0;

        try {
            $quiz->attempt()->save($quizAttempt); //save model with relation
            $quiz->refresh(); //reload relations so newly added model should be shown
            return true;
        } catch (\Exception $e) {
            Log::error($e);
            return false;
        }
    }

    private function insertQuizAttemptDetail($quiz, $quizQuestions)
    {
        $finalQuestionsArray = [];
        //iterate over question and make final question array
        foreach ($quizQuestions as $quizQuestion) {
            $questionArray = json_decode($quizQuestion->question, true);
            $questionArray['type'] = $quizQuestion->type->name;
            $finalQuestionsArray[] = $questionArray;
        }

        $quizAttemptDetail = new QuizAttemptDetail();
        $quizAttemptDetail->start_time = Carbon::now();
        //if quiz have time limit then set it otherwise keep null
        $quizAttemptDetail->expected_end_time = !empty($quiz->time_limit) ? Carbon::now()->addMinutes($quiz->time_limit) : null;
        $quizAttemptDetail->quiz_given = json_encode($finalQuestionsArray);
        $quizAttemptDetail->quiz_attempt = json_encode([]);

        $quizAttemptDetail->attempt()->associate($quiz->attempt);
        $quizAttemptDetail->save();

        return $quizAttemptDetail;

    }
    //This function shows the result in landing page after quiz submission
    public function finish($quizId)
    {
        //{"total":10,"courseId":6,"attempts":3,"grade":"PASS","grade_percentage":100}
//        $quiz = DB::table('quizzes')
//            ->join('quiz_attempts', 'quizzes.id', '=', 'quiz_attempts.fk_quiz_id')
//            ->select('quizzes.attempts_allowed as total','quizzes.fk_course_id as courseId', 'quiz_attempts.total_attempts as attempts', 'quiz_attempts.grading_final_result as grade','quiz_attempts.grading_percentage as grade_percentage')
//            ->where('quiz_attempts.fk_user_id',Auth::id())
//            ->where('quizzes.id',$quizId)
//            ->first();

        $quizz = DB::table('quizzes')
            ->join('quiz_attempts', 'quizzes.id', '=', 'quiz_attempts.fk_quiz_id')
            ->join('quiz_attempts_details', 'quiz_attempts.id', '=', 'quiz_attempts_details.fk_quiz_attempt_id')
            ->select('quizzes.attempts_allowed as total','quizzes.fk_course_id as courseId',
                'quiz_attempts.total_attempts as attempts', 'quiz_attempts_details.result as grade',
                'quiz_attempts_details.percentage as grade_percentage','quiz_attempts_details.quiz_attempt', 'quiz_attempts_details.quiz_given')
            ->where('quiz_attempts.fk_user_id', Auth::id())
            ->where('quizzes.id', $quizId)
            ->get()->toArray();

        $quiz = end($quizz);


        $message = '';

        $bool = true;
        if ($quiz->grade=='PASS'){
            $message = 'You have passed. '.'Your grade is '.(string)$quiz->grade_percentage .'%.';
        }else if ($quiz->attempts<$quiz->total){
            $message =  'Please try again. '.'Your grade is '.(string)$quiz->grade_percentage .'%.';
            $bool =false;
        }else{
            $message = 'Contact with your Line Manager. '.'Your grade is '.(string)$quiz->grade_percentage .'%.';
            $bool =false;
        }

        return view('quiz.quiz-landing',[
            'message' => $message,
            'bool' => $bool,
            'courseId' => $quiz->courseId,
        ]);

    }

}
