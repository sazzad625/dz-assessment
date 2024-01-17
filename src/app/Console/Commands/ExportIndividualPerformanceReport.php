<?php

namespace App\Console\Commands;

use App\Exports\ExportExcel;
use App\Helpers\PathHelper;
use App\Models\ReportExport;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Util;
use Excel;

/**
 * @author Ausaf
 * Class ImportSellers
 * @package App\Console\Commands
 */
class ExportIndividualPerformanceReport extends Command
{
    /**
     * The name and signature of the console command.
     * command: php artisan import:user "/file.csv"
     * @var string
     */
    protected $signature = 'export:individual-performance-report
    {studentIds : student ids} {reportExportId : reportExport id}';

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

        $reportExport = ReportExport::find($this->argument("reportExportId"));
        $reportExport->status = ReportExport::STATUS_IN_PROGRESS;
        $reportExport->save();

        try {
            $path = $this->generateFile($studentIds);
            $reportExport->path = $path;
            $reportExport->status = ReportExport::STATUS_COMPLETE;
            $reportExport->save();
        } catch (\Exception $e) {
            $reportExport->status = ReportExport::STATUS_FAILED;
            $reportExport->save();
            throw $e;
        }
    }

    private function generateFile($studentIds)
    {
        $columnArray = ['First Name', 'Last Name', 'Venture', 'Employee ID', 'WFM ID', 'Hub Name', 'City Name', 'Department',
            'Email Address', 'Course 1', 'Grade 1', 'Status 1'];

        $data = User::on('mysql_readonly')
            ->select('first_name', 'last_name', 'users.id', 'countries.name AS venture', 'employee_id',
                'wfm_id', 'hub_name', 'city_name',
                'departments.name AS department', 'email', 'courses.name AS courseName',
                DB::raw('sum(quiz_attempts.grading_percentage) AS percentage'),
                DB::raw('count(quiz_attempts.id) AS attempted'),
                DB::raw('count(quizzes.id) AS totalQuizzes'))
            ->join('countries', 'countries.id', '=', 'users.fk_country_id')
            ->join('departments', 'departments.id', '=', 'users.fk_department_id')
            ->leftJoin('course_user', 'users.id', '=', 'course_user.fk_user_id')
            ->leftJoin('courses', 'courses.id', '=', 'course_user.fk_course_id')
            ->leftJoin('quizzes', 'courses.id', '=', 'quizzes.fk_course_id')
            ->leftJoin('quiz_attempts', function ($join) {
                $join->on('quizzes.id', '=', 'quiz_attempts.fk_quiz_id');
                $join->on('quiz_attempts.fk_user_id', '=', 'users.id');
            })
            ->whereIn('users.id', $studentIds)
            ->whereNull('quizzes.deleted_at')
            ->groupBy('course_user.fk_user_id', 'first_name', 'last_name', 'countries.name',
                'users.employee_id', 'wfm_id', 'hub_name', 'city_name', 'departments.name', 'users.email', 'courses.name', 'users.id')
            ->get()->toArray();

        $array = [];
        $maxColumn = 1;
        foreach ($data as $key => $value) {
            $data[$key]['percentage'] = !empty($value['percentage']) && !empty($value['totalQuizzes'])
                ? $value['percentage'] / $value['totalQuizzes'] : 0;

            if ($value['totalQuizzes'] == 0) {
                $data[$key]['status'] = "No Quiz Found";
            }  else if ($value['attempted'] == 0) {
                $data[$key]['status'] = "Not Started";
            } else if ($value['attempted'] == $value['totalQuizzes']) {
                $data[$key]['status'] = "Completed";
            } else {
                $data[$key]['status'] = "In Progress";
            }
            unset($data[$key]['attempted'], $data[$key]['totalQuizzes']);

            if (empty($array[$value['id']])) {
                $array[$value['id']] = $data[$key];
                $array[$value['id']]['course1'] = $value['courseName'];
                $array[$value['id']]['grade1'] =  $data[$key]['percentage'];
                $array[$value['id']]['status1'] = $data[$key]['status'];
                unset($array[$value['id']]['percentage'], $array[$value['id']]['courseName'],
                    $array[$value['id']]['status'], $array[$value['id']]['id']);
            } else {
                $i = 1;
                while (!empty($array[$value['id']]['course' . $i])) {
                    $i++;
                }
                $array[$value['id']]['course' . $i] = $value['courseName'];
                $array[$value['id']]['grade' . $i] = $data[$key]['percentage'];
                $array[$value['id']]['status' . $i] = $data[$key]['status'];
                $maxColumn = $maxColumn < $i ? $i : $maxColumn;
            }
        }

        for ($i = 2; $i <= $maxColumn; $i++) {
            $columnArray[] = 'Course ' . $i;
            $columnArray[] = 'Grade ' . $i;
            $columnArray[] = 'Status ' . $i;
        }

        $fileName = PathHelper::getFileName(Util::normalizePath('IndividualPerformanceReportExportFile.xlsx'));
        $path = PathHelper::getNewTempPath();

        Excel::store(new ExportExcel($array, $columnArray), $path . $fileName);

        return $path . $fileName;
    }
}
