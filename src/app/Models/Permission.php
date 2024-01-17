<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    protected $table = "permissions";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /*
     * The Permission that belong to the roles.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission',
            'fk_permission_id', 'fk_role_id');
    }

    //Category
    const CREATE_COURSE_CATEGORY_PERMISSION = "Create Course Category";
    const UPDATE_COURSE_CATEGORY_PERMISSION = "Update Course Category";
    const DELETE_COURSE_CATEGORY_PERMISSION = "Delete Course Category";
    const VIEW_COURSE_CATEGORY_PERMISSION = "View Course Category";

    //Course
    const CREATE_COURSE_PERMISSION = "Create Course";
    const UPDATE_COURSE_PERMISSION = "Update Course";
    const DELETE_COURSE_PERMISSION = "Delete Course";
    const VIEW_COURSE_PERMISSION = "View Course";
    const SEARCH_COURSE_PERMISSION = "Search Course";
    const SEARCH_COURSE_PARTICIPANT_PERMISSION = "Search Course Participants";
    const ENROLL_COURSE_PARTICIPANT_PERMISSION = "Enroll Course Participants";
    const REMOVE_COURSE_PARTICIPANT_PERMISSION = "Remove Course Participant";
    const BULK_UPLOAD_COURSE_PARTICIPANT_PERMISSION = "Bulk Upload Course Participant";
    const CLONE_COURSE_PERMISSION = "Clone Course";

    //ACL Role permission
    const CREATE_ROLE_PERMISSION = "Create Role";
    const UPDATE_ROLE_PERMISSION = "Update Role";
    const VIEW_ROLE_PERMISSION = "View Role";

    //Student Permissions
    const VIEW_STUDENT_PERMISSION = "View Student";
    const CREATE_STUDENT_PERMISSION = "Create Student";
    const UPDATE_STUDENT_PERMISSION = "Update Student";
    const DELETE_STUDENT_PERMISSION = "Delete Student";
    const BULK_UPLOAD_STUDENT_PERMISSION = "Bulk Upload Student";
    const SEARCH_STUDENT_PERMISSION = "Search Student";

    //Teacher Permissions
    const VIEW_TEACHER_PERMISSION = "View Teacher";
    const CREATE_TEACHER_PERMISSION = "Create Teacher";
    const UPDATE_TEACHER_PERMISSION = "Update Teacher";
    const DELETE_TEACHER_PERMISSION = "Delete Teacher";
    const BULK_UPLOAD_TEACHER_PERMISSION = "Bulk Upload Teacher";
    const SEARCH_TEACHER_PERMISSION = "Search Teacher";

    //DEPARTMENT permission
    const CREATE_DEPARTMENT_PERMISSION = "Create Department";
    const UPDATE_DEPARTMENT_PERMISSION = "Update Department";
    const DELETE_DEPARTMENT_PERMISSION = "Delete Department";
    const SEARCH_DEPARTMENT_PERMISSION = "Search Department";

    //File Manager
    const FILE_MANAGER_PERMISSION = "File Manager";

    //Course content
    const MANAGE_COURSE_CONTENT_PERMISSION = "Manage Course Content";

    //Assets
    const ACCESS_SECURE_ASSETS_PERMISSION = "Access Secure Assets";

    const INDIVIDUAL_PERFORMANCE_REPORT_PERMISSION = "Individual Performance Report";
    const INDIVIDUAL_PERFORMANCE_REPORT_DOWNLOAD_PERMISSION = "Individual Performance Report Download";

    const CREATE_QUIZ_PERMISSION = "Create Quiz";
    const UPDATE_QUIZ_PERMISSION = "Update Quiz";
    const SEARCH_QUIZ_PERMISSION = "Search Quiz";
    const DELETE_QUIZ_PERMISSION = "Delete Quiz";

    const CREATE_QUIZ_QUESTION_PERMISSION = "Create Quiz Question";
    const UPDATE_QUIZ_QUESTION_PERMISSION = "Update Quiz Question";
    const DELETE_QUIZ_QUESTION_PERMISSION = "Delete Quiz Question";

    const COURSE_PERFORMANCE_REPORT_PERMISSION = "Course Performance Report";
    const COURSE_PERFORMANCE_REPORT_EXPORT_PERMISSION = "Course Performance Report Export";
    const REPORT_DOWNLOAD_PERMISSION = "Report Download";

    //Quiz review
    const SEARCH_QUIZ_REVIEW_PERMISSION = "Search Quiz Review";
    const QUIZ_REVIEW_PERMISSION = "Quiz Review";

}
