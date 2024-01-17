<?php

namespace App\Http\Controllers\Quiz;

use App\Helpers\AuthHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Quiz\AddQuizRequest;
use App\Http\Requests\Quiz\UpdateQuizRequest;
use App\Models\Course;
use App\Models\Permission;
use App\Models\QuestionType;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QuizController extends Controller
{
    public function search($courseId)
    {
        AuthHelper::hasPermissionElseAbort(Permission::SEARCH_QUIZ_PERMISSION);
        $course = Course::findOrFail($courseId);
        $course->load([
            'quizzes' =>
                function ($query) {
                    $query->orderBy('id', 'DESC');
                },
            'quizzes.grading'
        ]);

        return view('quiz.search', [
            'courseId' => $courseId,
            'quizzes' => $course->quizzes
        ]);
    }

    public function activeInactive($quizId, Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::UPDATE_QUIZ_PERMISSION);

        $request->validate([
            'active' => "required|boolean"
        ]);

        $quiz = Quiz::findOrFail($quizId);
        $quiz->is_active = $request->active;
        $quiz->save();

        return [
            'message' => 'success'
        ];

    }

    public function addQuiz($courseId)
    {
        AuthHelper::hasPermissionElseAbort(Permission::CREATE_QUIZ_PERMISSION);
        Course::findOrFail($courseId);

        return view('quiz.manage', [
            'courseId' => $courseId
        ]);
    }

    public function handleAddQuiz($courseId, AddQuizRequest $request)
    {
        $course = Course::findOrFail($courseId);

        $quiz = new Quiz();
        $quiz->name = $request->name;
        $quiz->description = $request->description;

        if (!empty($request->startDate)) {
            $quiz->start_time = $request->startDate;
        }

        if (!empty($request->endDate)) {
            $quiz->end_time = $request->endDate;
        }

        if (isset($request->timeLimit)) {
            $quiz->time_limit = $request->timeLimit;
        }

        $quiz->passing_percentage = $request->passingPercentage;
        $quiz->attempts_allowed = $request->attemptsAllowed;
        $quiz->type = $request->type;
        $quiz->allow_review = $request->allowReview;
        $quiz->max_questions = $request->maxQuestions;
        $quiz->is_active = $request->isActive;

        $quiz->course()->associate($course);
        $quiz->fk_quiz_grading_type_id = $request->gradingTypeId;

        try {
            $quiz->save();
        } catch (\Exception $e) {
            return response()->json(['message' => 'unable to save'], 500);
        }

        return [
            'message' => 'success',
            'id' => $quiz->id
        ];
    }

    public function updateQuiz($quizId)
    {
        AuthHelper::hasPermissionElseAbort(Permission::UPDATE_QUIZ_PERMISSION);
        $quiz = Quiz::findOrFail($quizId);

        if (empty($quiz)) {
            abort(404);
        }

        return view('quiz.manage', [
            'quizId' => $quizId,
            'courseId' => $quiz->fk_course_id
        ]);
    }

    public function handleUpdateQuiz($quizId, UpdateQuizRequest $request)
    {
        $quiz = Quiz::findOrFail($quizId);

        $quiz->name = $request->name;
        $quiz->description = $request->description;

        if (!empty($request->startDate)) {
            $quiz->start_time = $request->startDate;
        }

        if (!empty($request->endDate)) {
            $quiz->end_time = $request->endDate;
        }

        if (isset($request->timeLimit)) {
            $quiz->time_limit = $request->timeLimit;
        }

        $quiz->passing_percentage = $request->passingPercentage;
        $quiz->attempts_allowed = $request->attemptsAllowed;
        $quiz->type = $request->type;
        $quiz->allow_review = $request->allowReview;
        $quiz->max_questions = $request->maxQuestions;
        $quiz->is_active = $request->isActive;

        $quiz->fk_quiz_grading_type_id = $request->gradingTypeId;

        try {
            $quiz->save();
        } catch (\Exception $e) {
            return response()->json(['message' => 'unable to save'], 500);
        }

        return [
            'message' => 'success'
        ];
    }

    public function fetchQuiz($id)
    {
        AuthHelper::hasPermissionElseAbort(Permission::UPDATE_QUIZ_PERMISSION);
        $quiz = Quiz::findOrFail($id);
        $attemptedCount = QuizAttempt::where('fk_quiz_id', $id)->count();

        return [
            'id' => $quiz->id,
            'name' => $quiz->name,
            'description' => $quiz->description,
            'startDate' => !empty($quiz->start_time) ? $quiz->start_time->toDefaultDBDateFormat() : null,
            'endDate' => !empty($quiz->end_time) ? $quiz->end_time->toDefaultDBDateFormat() : null,
            'timeLimit' => $quiz->time_limit,
            'passingPercentage' => $quiz->passing_percentage,
            'attemptsAllowed' => $quiz->attempts_allowed,
            'type' => $quiz->type,
            'allowReview' => empty($quiz->allow_review) ? false : true,
            'maxQuestions' => $quiz->max_questions,
            'isActive' => empty($quiz->is_active) ? false : true,
            'gradingTypeId' => $quiz->fk_quiz_grading_type_id,
            'isQuizAttempted' => $attemptedCount == 0 ? false : true
        ];

    }

    public function fetchQuizQuestions($quizId)
    {
        AuthHelper::hasPermissionAnyElseAbort([
            Permission::CREATE_QUIZ_QUESTION_PERMISSION,
            Permission::UPDATE_QUIZ_QUESTION_PERMISSION
        ]);

        $questions = QuizQuestion::where('fk_quiz_id', $quizId)->get();
        $questions->load('type');

        $questionsJson = [];

        foreach ($questions as $question) {
            $questionsJson[] = [
                'id' => $question->id,
                'type' => $question->type->name,
                'name' => $question->name,
                'json' => json_decode($question->question)
            ];
        }

        return [
            'message' => 'success',
            'questions' => $questionsJson
        ];

    }

    public function handleCreateQuestion($quizId, Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::CREATE_QUIZ_QUESTION_PERMISSION);
        $quiz = Quiz::findOrFail($quizId);
        $quiz->load('course', 'course.category');

        $request->validate([
            'type' => 'required|exists:question_types,name',
            'name' => 'required|max:255',
            'json' => 'required|json'
        ]);

        $questionType = QuestionType::where('name', $request->type)->first();
        $question = new QuizQuestion();
        $question->category()->associate($quiz->course->category);
        $question->quiz()->associate($quiz);
        $question->type()->associate($questionType);
        $question->createdBy()->associate(Auth::user());
        $question->name = $request->name;
        $question->question = $request->json;

        try {
            $question->save();
            return ['message' => 'success', "id" => $question->id];
        } catch (\Exception $e) {
            Log::error($e);
            return response(['message' => 'Unable to save'], 500);
        }
    }

    public function handleUpdateQuestion($questionId, Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::UPDATE_QUIZ_QUESTION_PERMISSION);

        $request->validate([
            'type' => 'required|exists:question_types,name',
            'name' => 'required|max:255',
            'json' => 'required|json'
        ]);

        $questionType = QuestionType::where('name', $request->type)->first();
        $question = QuizQuestion::findOrFail($questionId);
        $question->type()->associate($questionType);
        $question->name = $request->name;
        $question->question = $request->json;

        try {
            $question->save();
            return ['message' => 'success', "id" => $questionId];
        } catch (\Exception $e) {
            return response(['message' => 'Unable to update'], 500);
        }
    }

    public function handleDeleteQuestion($questionId)
    {
        AuthHelper::hasPermissionElseAbort(Permission::DELETE_QUIZ_QUESTION_PERMISSION);

        $question = QuizQuestion::findOrFail($questionId);
        $question->delete();

        return ['message' => 'success'];

    }

}
