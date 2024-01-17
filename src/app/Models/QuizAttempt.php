<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizAttempt extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "quiz_attempts";

    const RESULT_PASS = "PASS";
    const RESULT_FAILED = "FAILED";

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'fk_quiz_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'fk_user_id', 'id');
    }

    public function grading()
    {
        return $this->belongsTo(QuizGradingType::class, 'fk_quiz_grading_type_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(QuizAttemptDetail::class, 'fk_quiz_attempt_id', 'id');
    }

    public function saveAggregatedData()
    {
        $this->load('details', 'quiz', 'grading');
        $percentage = 0;
        $passingPercentage = $this->quiz->passing_percentage;

        switch ($this->grading->code_name) {
            case QuizGradingType::TYPE_HIGHEST:
            {
                $percentage = $this->details->max('percentage');
                break;
            }
            case QuizGradingType::TYPE_AVERAGE:
            {
                $percentage = round($this->details->avg('percentage'));
                break;
            }
            case QuizGradingType::TYPE_FIRST_ATTEMPT:
            {
                $percentage = $this->details->first()->percentage;
                break;
            }
            case QuizGradingType::TYPE_LAST_ATTEMPT:
            {
                $percentage = $this->details->last()->percentage;
                break;
            }
        }

        $result = $percentage >= $passingPercentage ? self::RESULT_PASS : self::RESULT_FAILED;

        $this->grading_percentage = $percentage;
        $this->grading_final_result = $result;

        $this->save();
    }
}
