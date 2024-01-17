<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseContentAttachment extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "course_contents_attachments";

    public function content()
    {
        return $this->belongsTo(CourseContent::class, 'fk_course_contents_id', 'id');
    }
}
