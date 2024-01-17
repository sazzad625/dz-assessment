<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $table = 'courses';

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'course_user',
            'fk_course_id',
            'fk_user_id'
        );
    }

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'fk_course_categories_id', 'id')->withTrashed();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'fk_created_by', 'id');
    }

    public function content()
    {
        return $this->hasOne(CourseContent::class, 'fk_courses_id');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'fk_course_id', 'id');
    }
}
