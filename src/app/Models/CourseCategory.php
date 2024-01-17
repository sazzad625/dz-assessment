<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseCategory extends Model
{
    use SoftDeletes;

    protected $table = 'course_categories';

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'fk_created_by');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'fk_course_categories_id', 'id');
    }

    public function getImage()
    {
        return route('public.storage',  $this->image_path . $this->image_name);
    }
}
