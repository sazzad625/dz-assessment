<?php

namespace App\Http\Controllers\Category;

use App\Helpers\AuthHelper;
use App\Helpers\OssStorageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Course\AddCourseCategoryRequest;
use App\Http\Requests\Course\UpdateCourseCategoryRequest;
use App\Models\CourseCategory;
use App\Models\Permission;
use App\Models\User;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    const IMAGE_WIDTH = 500;
    const IMAGE_HEIGHT = 280;

    public function index()
    {
        AuthHelper::hasPermissionElseAbort(Permission::VIEW_COURSE_CATEGORY_PERMISSION);

        $allCategory = CourseCategory::get();
        return view('category.view', ['categories' => $allCategory]);
    }

    public function addCourseCategory()
    {
        AuthHelper::hasPermissionElseAbort(Permission::CREATE_COURSE_CATEGORY_PERMISSION);

        return view('category.add');
    }

    public function handleAddCourseCategory(AddCourseCategoryRequest $request)
    {
        $storedFileInfo = OssStorageHelper::storeUploadedImageToPublic(
            $request->file('image'),
            self::IMAGE_WIDTH,
            self::IMAGE_HEIGHT
        );

        $courseCategory = new CourseCategory();
        $courseCategory->name = $request->name;
        $courseCategory->description = $request->description;
        $courseCategory->is_active = $request->active == true;
        $courseCategory->image_path = $storedFileInfo['path'];
        $courseCategory->image_name = $storedFileInfo['name'];
        $courseCategory->createdBy()->associate(Auth::user());
        $courseCategory->save();

        return redirect(route('category.update', $courseCategory->id));
    }

    public function updateCourseCategory($id)
    {
        AuthHelper::hasPermissionElseAbort(Permission::UPDATE_COURSE_CATEGORY_PERMISSION);

        $courseCategory = CourseCategory::find($id);
        if (empty($courseCategory)) {
            abort(404);
        }

        return view('category.update', [
            'row' => $courseCategory
        ]);

    }

    public function handleUpdateCourseCategory($id, UpdateCourseCategoryRequest $request)
    {
        $courseCategory = CourseCategory::find($id);

        if (!empty($request->file('image'))) {

            $storedFileInfo = OssStorageHelper::storeUploadedImageToPublic(
                $request->file('image'),
                self::IMAGE_WIDTH,
                self::IMAGE_HEIGHT
            );

            $courseCategory->image_path = $storedFileInfo['path'];
            $courseCategory->image_name = $storedFileInfo['name'];
        }

        $courseCategory->name = $request->name;
        $courseCategory->description = $request->description;
        $courseCategory->is_active = $request->active == true;
        $courseCategory->save();

        return redirect(route('category.view'))->with('success','Record updated successfully');
    }

    public function deleteCourseCategory(Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::DELETE_COURSE_CATEGORY_PERMISSION);

        $request->validate(['id' => 'required']);
        CourseCategory::destroy($request->id);
        return Response::json(['message' => 'Record deleted successfully'], 200);

    }
}
