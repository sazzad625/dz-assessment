<?php

namespace App\Http\Controllers\Course;

use App\Helpers\AuthHelper;
use App\Helpers\PathHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Course\AddCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Models\Course;
use App\Models\CourseContent;
use App\Models\CourseContentAttachment;
use App\Models\Permission;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function addCourse()
    {
        AuthHelper::hasPermissionElseAbort(Permission::CREATE_COURSE_PERMISSION);

        return view('course.add');
    }

    public function handleAddCourse(AddCourseRequest $request)
    {
        $course = new Course();
        $course->name = $request->name;
        $course->short_name = $request->short_name;
        $course->start_date = $request->start_date;
        $course->end_date = $request->end_date;
        $course->is_active = $request->active == true;
        $course->fk_course_categories_id = $request->category;
        $course->createdBy()->associate(Auth::user());

        DB::beginTransaction();
        $course->save();

        $courseContent = new CourseContent();
        $courseContent->course()->associate($course);
        $courseContent->title = '';
        $courseContent->path = '';
        $courseContent->createdBy()->associate(Auth::user());
        try{
            $courseContent->save();
            DB::commit();
        }catch (\Exception $e){
            Log::error($e);
            DB::rollBack();
            return redirect()->back()->with('status.error', 'Unable to create course')->withInput();
        }

        return redirect(route('course.update', $course->id))->with('status.success', 'Course created successfully');
    }

    public function updateCourse($id)
    {
        AuthHelper::hasPermissionElseAbort(Permission::UPDATE_COURSE_PERMISSION);

        $course = Course::findOrFail($id);

        return view('course.update', [
            'course' => $course
        ]);

    }

    public function handleUpdateCourse($id, UpdateCourseRequest $request)
    {
        $course = Course::findOrFail($id);
        $course->name = $request->name;
        $course->short_name = $request->short_name;
        $course->start_date = $request->start_date;
        $course->end_date = $request->end_date;
        $course->is_active = $request->active == true;
        $course->fk_course_categories_id = $request->category;
        $course->save();

        return redirect(route('course.update', $course->id))->with('status.success','Record updated successfully');
    }

    public function search(Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::SEARCH_COURSE_PERMISSION);
        $search = Course::with(['createdBy' =>function ($query) {
            $query->withTrashed();
        }, 'users']);

        if (!empty($request->name)) {
            $search = $search->where('name', 'like', "%{$request->name}%");
        }

        return view('course.search', [
            'search' => $search->paginate()
        ]);
    }

    public function delete(Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::DELETE_COURSE_PERMISSION);
        $request->validate([
            'id' => 'required'
        ]);

        $user = Course::findOrFail($request->id);
        $user->delete();

        return Response::json([
            'message' => "success"
        ]);
    }

    public function clone(Course $course)
    {
        AuthHelper::hasPermissionElseAbort(Permission::CLONE_COURSE_PERMISSION);

        $newCourse = new Course();
        $newCourse->name = "[cloned] " .$course->name;
        $newCourse->short_name = substr("clone ".rand(0,9)."-".$course->short_name,0,20) ;
        $newCourse->start_date = $course->start_date;
        $newCourse->end_date = $course->end_date;
        $newCourse->is_active = $course->is_active;
        $newCourse->fk_course_categories_id = $course->category->id;
        $newCourse->createdBy()->associate(Auth::user());

        DB::beginTransaction();
        try{
            $newCourse->save();

            $courseContent = new CourseContent();
            $courseContent->course()->associate($newCourse);
            $courseContent->title = $course->content->title;
            $courseContent->path = '';
            $courseContent->createdBy()->associate(Auth::user());
            $courseContent->save();

            if(!empty($course->content->path)){
                $courseContent->path = $this->cloneContent($newCourse->id,$courseContent->id,$course->content->path);
                $courseContent->save();
            }

            if(count($course->content->attachments)>0) {
                foreach ($course->content->attachments as $attachment) {
                    $courseAttachement = new CourseContentAttachment();
                    $courseAttachement->path = $attachment->path;
                    $courseAttachement->content()->associate($courseContent);
                    $courseAttachement->save();
                }
            }

            if(count($course->quizzes) > 0)
            {
                foreach ($course->quizzes as $quiz)
                {
                    $newCourseQuiz = new Quiz();
                    $newCourseQuiz->name = $quiz->name;
                    $newCourseQuiz->description = $quiz->description;
                    $newCourseQuiz->start_time = $quiz->start_time;
                    $newCourseQuiz->end_time = $quiz->end_time;
                    $newCourseQuiz->time_limit = $quiz->time_limit;
                    $newCourseQuiz->passing_percentage = $quiz->passing_percentage;
                    $newCourseQuiz->attempts_allowed = $quiz->attempts_allowed;
                    $newCourseQuiz->type = $quiz->type;
                    $newCourseQuiz->allow_review = $quiz->allow_review;
                    $newCourseQuiz->max_questions = $quiz->max_questions;
                    $newCourseQuiz->is_active = $quiz->is_active;
                    $newCourseQuiz->course()->associate($newCourse);
                    $newCourseQuiz->fk_quiz_grading_type_id = $quiz->fk_quiz_grading_type_id;
                    $newCourseQuiz->save();

                    if(count($quiz->quizQuestions)>0)
                    {
                        foreach($quiz->quizQuestions as $quizQuestion)
                        {
                            $newQuizQuestion = new QuizQuestion();
                            $newQuizQuestion->fk_course_categories_id = $quizQuestion->fk_course_categories_id;
                            $newQuizQuestion->quiz()->associate($newCourseQuiz);
                            $newQuizQuestion->fk_question_types_id = $quizQuestion->fk_question_types_id;
                            $newQuizQuestion->createdBy()->associate(Auth::user());
                            $newQuizQuestion->name = $quizQuestion->name;
                            $newQuizQuestion->question = $quizQuestion->question;
                            $newQuizQuestion->save();
                        }
                    }
                }
            }

            DB::commit();
        }catch (\Exception $e){
            Log::error($e);
            DB::rollBack();
            return redirect()->back()->with('status.error', 'Unable to clone course')->withInput();
        }

        return redirect()->back()->with('status.success', 'Course cloned successfully');
    }

    private function cloneContent($courseId,$courseContentId,$path)
    {
        $contentBody = Storage::get($path);
        $path = PathHelper::getNewCourseContentPath() . PathHelper::getFileName($courseId . "_" . $courseContentId . ".html");
        Storage::put($path, $contentBody);
        return $path;
    }
}
