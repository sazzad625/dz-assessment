<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "question_types";

    /**
     * This is a sample json for Multi Choice
     * {
     *  text: 'Choose correct answer from below options',
     *  options: [
     *      {text: 'option 1', isCorrect: true},
     *      {text: 'option 2', isCorrect: false}
     *  ]
     * }
     */
    const TYPE_MULTI_CHOICE = "Multi Choice";

    public static function getAllForOptions()
    {
        return QuestionType::select('id', 'name')->get()->pluck('name', 'id');
    }


}
