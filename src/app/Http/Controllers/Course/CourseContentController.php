<?php

namespace App\Http\Controllers\Course;

use App\Helpers\AuthHelper;
use App\Helpers\PathHelper;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseContent;
use App\Models\CourseContentAttachment;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseContentController extends Controller
{
    public function manage($courseId)
    {
        AuthHelper::hasPermissionElseAbort(Permission::MANAGE_COURSE_CONTENT_PERMISSION);

        $course = Course::findOrFail($courseId);
        $course->load('content');

        if (empty($course->content)) {
            abort(404);
        }

        return view('course-content.manage', [
            'courseId' => $courseId,
            'contentId' => $course->content->id
        ]);
    }

    public function getContent($courseId, $contentId)
    {
        AuthHelper::hasPermissionElseAbort(Permission::MANAGE_COURSE_CONTENT_PERMISSION);

        $courseContent = CourseContent::where('id', $contentId)->where('fk_courses_id', $courseId)->first();

        if (empty($courseContent)) {
            abort(404);
        }

        return [
            "contentBody" => !empty($courseContent->path) ? Storage::get($courseContent->path) : ''
        ];
    }

    public function saveContent($courseId, $contentId, Request $request)
    {
        AuthHelper::hasPermissionElseAbort(Permission::MANAGE_COURSE_CONTENT_PERMISSION);

        $request->validate([
            "contentBody" => 'required'
        ]);

        $courseContent = CourseContent::where('id', $contentId)->where('fk_courses_id', $courseId)->first();

        if (empty($courseContent)) {
            abort(404);
        }

        if (!empty($courseContent->path)) {
            Storage::put($courseContent->path, $request->contentBody);
            return;
        }

        $path = PathHelper::getNewCourseContentPath() . PathHelper::getFileName($courseId . "_" . $contentId . ".html");
        Storage::put($path, $request->contentBody);
        $courseContent->path = $path;
        $courseContent->save();

    }

    public function getContentAttachments($courseId, $contentId)
    {
        AuthHelper::hasPermissionElseAbort(Permission::MANAGE_COURSE_CONTENT_PERMISSION);

        $courseContent = CourseContent::where('id', $contentId)->where('fk_courses_id', $courseId)->first();

        if (empty($courseContent)) {
            abort(404);
        }

        $courseContent->load('attachments');

        $result = [];

        foreach ($courseContent->attachments as $attachment) {
            $result[] = [
                'id' => $attachment->id,
                'uri' => $attachment->path
            ];
        }

        return [
            'data' => $result
        ];

    }

    public function deleteContentAttachment($courseId, $contentId, Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        AuthHelper::hasPermissionElseAbort(Permission::MANAGE_COURSE_CONTENT_PERMISSION);

        $attachment = CourseContentAttachment::where('id', $request->id)->where('fk_course_contents_id',
            $contentId)->first();

        if (empty($attachment)) {
            abort(404);
        }

        $attachment->delete();
    }

    public function addContentAttachment($courseId, $contentId, Request $request)
    {
        $request->validate([
            'uri' => 'required'
        ]);

        AuthHelper::hasPermissionElseAbort(Permission::MANAGE_COURSE_CONTENT_PERMISSION);

        $courseContent = CourseContent::where('id', $contentId)->where('fk_courses_id', $courseId)->first();

        if (empty($courseContent)) {
            abort(404);
        }

        $attachment = new CourseContentAttachment();
        $attachment->path = $request->uri;

        $attachment->content()->associate($courseContent);
        $attachment->save();

        return [
            "id" => $attachment->id
        ];
    }

}
