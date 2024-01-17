<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseContent extends Model
{
    use HasFactory;

    protected $table = "course_contents";

    public function attachments()
    {
        return $this->hasMany(CourseContentAttachment::class, 'fk_course_contents_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'fk_courses_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
