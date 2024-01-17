<?php

namespace App\Console\Commands;

use App\Mail\NotificationMail;
use App\Models\Country;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @author Ausaf
 * Class ImportSellers
 * @package App\Console\Commands
 */
class ImportCourseParticipant extends Command
{
    //csv column numbers
    const USERNAME = 0;
    private $errors = [];

    /**
     * The name and signature of the console command.
     * command: php artisan import:user "/file.csv"
     * @var string
     */
    protected $signature = 'import:course-participant
    {filePath : absolute path of the csv file} {userId : uploaded user id} {courseId : course id}';

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
        $this->errors = [];
        $notificationMail = new NotificationMail();
        $notificationMail->title = "Import Course Participant";
        $notificationMail->subject = "[OpsAcademy] | Import Course Participant Notification";

        $file_handle = Storage::disk('oss')->getDriver()->readStream($this->argument("filePath"));

        $courseId = $this->argument("courseId");
        $userId = $this->argument("userId");
        $user = User::find($userId);

        // to get headers
        $headers = fgetcsv($file_handle, 0, ",");
        $rowNumber = 2;
        while (!feof($file_handle)) {
            $row = fgetcsv($file_handle, 0, ",");
            if (empty($row)) {
                break;
            }

            //if validation fails then do not process this row
            if (!$this->validateRow($row, $rowNumber++)) {
                continue;
            }

            $this->insertUser($row, $courseId);
        }

        fclose($file_handle);

        $notificationMail->appendMessage("Rows scan : " . ($rowNumber-2));

        if (!empty($this->errors)) {
            $notificationMail->appendMessage("Rows with errors : " . count($this->errors));
            $notificationMail->appendMessage("---------------------------- Errors Summary -------------------------------");

            foreach ($this->errors as $error) {
                $notificationMail->appendMessage($error);
            }
        } else {
            $notificationMail->appendMessage("Rows with errors : 0");
        }

        $notificationMail->setToEmail($user->email);
        $notificationMail->sendMail();
    }

    private function insertUser($row, $courseId)
    {
        $course = Course::find($courseId);
        if(empty($course))
        {
            $error = "Course not found {$row[self::USERNAME]}";
            $this->errors[] = $error;
            return ;
        }
        $user = User::where('name',$row[self::USERNAME])->where('type', User::USER_TYPE_STUDENT)->first();
        if(empty($user))
        {
            $error = "Student not found {$row[self::USERNAME]}";
            $this->errors[] = $error;
            return ;
        }

        try {
            $course->users()->attach($user);
        } catch (\Exception $ex) {
            $error = "unable to insert Student {$row[self::USERNAME]}";
            $this->errors[] = $error;
        }

    }

    private function validateRow($row, $rowNumber)
    {

        $request = new Request([
            'username' => $row[self::USERNAME]
        ]);

        $validator = Validator::make($request->all(), [
            'username' => 'required|exists:users,name'
        ]);

        if ($validator->fails()) {
            $error = "Invalid data for row {$rowNumber}, {$validator->errors()->first()}";
            $this->errors[] = $error;
            return false;
        }

        return true;
    }
}
