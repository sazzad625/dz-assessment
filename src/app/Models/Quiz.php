<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Quiz extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'quizzes';

    const TYPE_GRADED = 'GRADED';
    const TYPE_UNGRADED = 'UNGRADED';

    const TYPES = [
        self::TYPE_GRADED,
        self::TYPE_UNGRADED
    ];

    protected $dates = [
        'start_time',
        'end_time'
    ];

    public function grading()
    {
        return $this->belongsTo(QuizGradingType::class, 'fk_quiz_grading_type_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'fk_course_id', 'id')->withTrashed();
    }

    public function attempt($userId = null)
    {
        return $this->hasOne(QuizAttempt::class, 'fk_quiz_id', 'id')
            ->where('fk_user_id', !empty($userId) ? $userId : Auth::id());
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class, 'fk_quiz_id', 'id');
    }

    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class, 'fk_quiz_id', 'id');
    }
}
