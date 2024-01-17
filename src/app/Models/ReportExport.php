<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportExport extends Model
{
    const REPORT_TYPE_INDIVIDUAL_PERFORMANCE = 'individual-performance';
    const REPORT_TYPE_COURSE_PERFORMANCE = 'course-performance';

    const STATUS_QUEUED = "QUEUED";
    const STATUS_IN_PROGRESS = "IN PROGRESS";
    const STATUS_FAILED = "FAILED";
    const STATUS_COMPLETE = "COMPLETE";
    protected $table = "report_export";

}
