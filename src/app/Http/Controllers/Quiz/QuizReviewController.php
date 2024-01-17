<?php


namespace App\Http\Controllers\Quiz;


use App\Helpers\AuthHelper;
use App\Models\Course;
use App\Models\Permission;
use App\Models\QuizAttemptDetail;
use App\Quiz\Question;

class QuizReviewController
{
    public function index($courseId, $userId)
    {
        AuthHelper::hasPermissionElseAbort(Permission::SEARCH_QUIZ_REVIEW_PERMISSION);

        $course = Course::findOrFail($courseId);

        $course->load([
            'quizzes',
            'quizzes.attempts' => function ($query) use ($userId) {
                $query->where('fk_user_id', $userId);
            },
            'quizzes.attempts.details' => function ($query) {
                $query->select('id', 'fk_quiz_attempt_id', 'start_time', 'end_time', 'result', 'percentage');
                $query->where('status', QuizAttemptDetail::STATUS_COMPLETED);
            }
        ]);

        return view('quiz-review.index', [
            'course' => $course
        ]);
    }

    public function review($quizDetailId)
    {
        AuthHelper::hasPermissionElseAbort(Permission::QUIZ_REVIEW_PERMISSION);
        $quizAttemptDetail = QuizAttemptDetail::findOrFail($quizDetailId);

        //prevent if quiz is not submitted yet
        if ($quizAttemptDetail->status != QuizAttemptDetail::STATUS_COMPLETED) {
            abort(404);
        }

        //contains quiz given to student
        $questionsArray = json_decode($quizAttemptDetail->quiz_given, true);

        //contains the answers given by student
        $answersArray = json_decode($quizAttemptDetail->quiz_attempt, true);

        //contains final questions json which will be sent in response.
        $questions = [];

        foreach ($questionsArray as $index => $question) {
            $tempQuestion = Question::of($question);
            //if student given answer for this question then populate it
            if (array_key_exists($index, $answersArray)) {
                $tempQuestion->setAnswer($answersArray[$index]);
            }
            $questions[] = $tempQuestion->serialize(); //adding question to final array
        }

        return view('quiz-review.review', [
            'questions' => $questions
        ]);
    }
}
