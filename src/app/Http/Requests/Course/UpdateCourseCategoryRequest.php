<?php

namespace App\Http\Requests\Course;

use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCourseCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->hasPermission(Permission::UPDATE_COURSE_CATEGORY_PERMISSION);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:125|unique:course_categories,name,'. $this->route('id') .',id,deleted_at,NULL',
            'description' => 'required|max:255',
            'image' => 'sometimes|required|mimes:jpg,jpeg,png'
        ];
    }
}
