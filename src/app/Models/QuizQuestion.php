<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizQuestion extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'quiz_questions';

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'fk_course_categories_id', 'id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'fk_quiz_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function type()
    {
        return $this->belongsTo(QuestionType::class, 'fk_question_types_id', 'id');
    }

    /*
     * This method returns all questions related to quiz
     * @param $quizId the quiz id
     * @return array the array of quiz questions model
     */
    public static function getQuizQuestions($quizId)
    {
        $quiz = Quiz::find($quizId);

        $questions = QuizQuestion::with('type')
            ->where('fk_quiz_id', $quizId)
            ->take($quiz->max_questions)->get();

        return !$questions->isEmpty() ? $questions : null;
    }

}
