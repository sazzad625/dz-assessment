<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizGradingType extends Model
{
    use HasFactory;

    protected $table = "quiz_grading_types";

    const TYPE_HIGHEST = "highest";
    const TYPE_AVERAGE = "average";
    const TYPE_FIRST_ATTEMPT = "first_attempt";
    const TYPE_LAST_ATTEMPT = "last_attempt";

    public static function getAllForOptions()
    {
        return QuizGradingType::select('id', 'name')->get()->pluck('name', 'id');
    }
}
