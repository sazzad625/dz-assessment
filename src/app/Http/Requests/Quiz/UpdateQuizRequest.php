<?php

namespace App\Http\Requests\Quiz;

use App\Helpers\AuthHelper;
use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return AuthHelper::hasPermission(Permission::UPDATE_QUIZ_PERMISSION);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:125',
            'description' => 'required|max:255',
            'passingPercentage' => 'required|min:1|max:100',
            'attemptsAllowed' => 'required|min:1|max:10',
            'allowReview' => 'required|boolean',
            'maxQuestions' => 'required|min:1',
            'isActive' => 'required|boolean',
            'gradingTypeId' => 'required|exists:quiz_grading_types,id',
            'type' => 'required|in:GRADED,UNGRADED'
        ];
    }
}
