<?php

namespace App\Console\Commands;

use App\Exports\ExportExcel;
use App\Helpers\PathHelper;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\ReportExport;
use Illuminate\Console\Command;
use League\Flysystem\Util;
use Excel;

/**
 * @author Ausaf
 * Class ImportSellers
 * @package App\Console\Commands
 */
class ExportCoursePerformanceReport extends Command
{
    /**
     * The name and signature of the console command.
     * command: php artisan import:user "/file.csv"
     * @var string
     */
    protected $signature = 'export:course-performance-report
    {studentIds : student ids} {reportExportId : reportExport id} {courseId : course id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $studentIds = $this->argument("studentIds");
        $courseId = $this->argument("courseId");

        $reportExport = ReportExport::find($this->argument("reportExportId"));
        $reportExport->status = ReportExport::STATUS_IN_PROGRESS;
        $reportExport->save();

        try {
            $path = $this->generateFile($studentIds, $courseId);
            $reportExport->path = $path;
            $reportExport->status = ReportExport::STATUS_COMPLETE;
            $reportExport->save();
        } catch (\Exception $e) {
            $reportExport->status = ReportExport::STATUS_FAILED;
            $reportExport->save();
            throw $e;
        }
    }

    private function generateFile($studentIds, $courseId)
    {
        $columnArray = ['First name', 'Last name', 'Venture', 'Employee ID', 'WFM ID', 'Hub Name', 'City Name',
            'Department', 'Email', 'Course Name'];

        $data = Course::on('mysql_readonly')
            ->select('users.id AS id', 'first_name As First name', 'last_name As Last name',
                'countries.name AS Venture', 'users.employee_id AS Employee ID',
                'wfm_id AS WFM ID', 'hub_name AS Hub Name', 'city_name AS City Name',
                'departments.name AS Department',
                'users.email AS Email', 'courses.name AS Course Name', 'quizzes.name AS quizzes',
                'quiz_attempts.grading_percentage AS percentage')
            ->join('course_user', 'course_user.fk_course_id', '=', 'courses.id')
            ->join('users', 'users.id', '=', 'course_user.fk_user_id')
            ->join('departments', 'departments.id', '=', 'users.fk_department_id')
            ->join('countries', 'countries.id', '=', 'users.fk_country_id')
            ->join('quizzes', 'courses.id', '=', 'quizzes.fk_course_id')
            ->leftjoin('quiz_attempts', function ($join) {
                $join->on('quiz_attempts.fk_quiz_id', '=', 'quizzes.id');
                $join->on('users.id', '=', 'quiz_attempts.fk_user_id');
            })
            ->where('courses.id', $courseId)->whereIn('users.id', $studentIds)
            ->orderBy('quizzes.id')->get()->toArray();

        $quizzesColumn = Quiz::select('name')->where('fk_course_id', $courseId)->get()->toArray();

        //arranging multi record of same user in one
        $array = [];
        foreach ($data as $key => $value) {
            if (empty($array[$value['id']])) {
                $array[$value['id']] = $value;
                $array[$value['id']][$value['quizzes']] = $value['percentage'];
            } else {
                $array[$value['id']][$value['quizzes']] = $value['percentage'];
            }
        }

        //formated array (order of quizzes may mismatch from column)
        $orderedArray = [];
        foreach ($array as $key => $value) {
            $orderedArray[$key] = array_slice($value, 1, 10);
            $sumOfPercentage = 0;
            foreach ($quizzesColumn as $column) {
                $orderedArray[$key][$column['name']] = isset($value[$column['name']]) ? (string)$value[$column['name']] : 'N/A';
                $sumOfPercentage += (int)$orderedArray[$key][$column['name']];
            }
            $orderedArray[$key]['avg'] = (string)($sumOfPercentage / count($quizzesColumn));
        }
        $columnArray = array_merge($columnArray, array_column($quizzesColumn, 'name'), ['Average%']);

        $fileName = PathHelper::getFileName('CoursePerformanceReportExportFile.xlsx');
        $path = PathHelper::getNewTempPath();

        Excel::store(new ExportExcel($orderedArray, $columnArray), $path . $fileName);

        return $path . $fileName;
    }
}
