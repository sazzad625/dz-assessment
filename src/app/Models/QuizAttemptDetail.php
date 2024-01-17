<?php

namespace App\Models;

use App\Quiz\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizAttemptDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "quiz_attempts_details";

    const STATUS_INITIATED = "INITIATED";
    const STATUS_COMPLETED = "COMPLETED";

    protected $dates = [
        'start_time',
        'end_time',
        'expected_end_time'
    ];

    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'fk_quiz_attempt_id', 'id');
    }

    public function saveCalculatedResult()
    {

        $this->load('attempt.quiz');

        $questions = json_decode($this->quiz_given, true);
        $answersGiven = json_decode($this->quiz_attempt); //answers giving by student
        $correctAnswersCount = 0;
        $totalQuestionsCount = count($questions);

        foreach ($questions as $index => $question) {
            $tempQuestion = Question::of($question);
            $tempQuestion->setAnswer(isset($answersGiven[$index]) ? $answersGiven[$index] : null);
            if ($tempQuestion->isCorrect()) {
                $correctAnswersCount++;
            }
        }
        if ($this->attempt->quiz && $this->attempt->quiz->passing_percentage) {
            $passingPercentage = $this->attempt->quiz->passing_percentage;
        }else{
            $passingPercentage = 0;
        }
        $percentage = round($correctAnswersCount / $totalQuestionsCount * 100); //percentage student got

        $this->percentage = $percentage;
        $this->result = $percentage >= $passingPercentage ?
            QuizAttempt::RESULT_PASS : QuizAttempt::RESULT_FAILED;

        $this->save();

    }
}
