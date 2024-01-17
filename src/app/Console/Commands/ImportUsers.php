<?php

namespace App\Console\Commands;

use App\Mail\NotificationMail;
use App\Models\Country;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @author Ausaf
 * Class ImportSellers
 * @package App\Console\Commands
 */
class ImportUsers extends Command
{
    //csv column numbers
    const EID = 0;
    const WFMID = 1;
    const FNAME = 2;
    const LNAME = 3;
    const NAME = 4;
    const PASSWORD = 5;
    const COUNTRY = 6;
    const EMAIL = 7;
    const DEPARTMENT = 8;
    const ROLE = 9;
    const HUB_NAME = 10;
    const CITY_NAME = 11;
    private $type;
    private $errors = [];

    /**
     * The name and signature of the console command.
     * command: php artisan import:user "/file.csv"
     * @var string
     */
    protected $signature = 'import:users
    {filePath : absolute path of the csv file} {userId : uploaded user id} {type : user type}';

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
        $notificationMail->title = "Import User";
        $notificationMail->subject = "[OpsAcademy] | Import User Notification";

        $file_handle = Storage::disk('oss')->getDriver()->readStream($this->argument("filePath"));

        $this->type = $this->argument("type");
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

            $this->insertUser($row);
        }

        fclose($file_handle);

        $notificationMail->appendMessage("Rows scan : " . ($rowNumber - 2));

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

    private function insertUser($row)
    {
        $user = new User();
        $user->employee_id = $row[self::EID];
        $user->wfm_id = $row[self::WFMID];
        $user->first_name = $row[self::FNAME];
        $user->last_name = $row[self::LNAME];
        $user->name = $row[self::NAME];
        $user->email = $row[self::EMAIL];
        $user->hub_name = $row[self::HUB_NAME];
        $user->city_name = $row[self::CITY_NAME];
        $user->password = $this->type == User::USER_TYPE_STUDENT ? bcrypt($row[self::PASSWORD]) : "123";
        $country = Country::where('code', $row[self::COUNTRY])->first();
        if (!empty($row[self::DEPARTMENT])) {
            $department = Department::where('name', $row[self::DEPARTMENT])->first();
        }

        if (empty($row[self::EMAIL])) {
            $user->email = $row[self::NAME] . '@domain.com';
        }

        if ($this->type == User::USER_TYPE_TEACHER) {
            $role = Role::where('name', strtoupper($row[self::ROLE]))->first();
        }

        $user->type = $this->type;
        try {
            if (!empty($department)) {
                $user->department()->associate($department);
            }
            $user->country()->associate($country);
            $user->save();
            if (!empty($role)) {
                $user->roles()->attach($role);
            }
            if ($this->type != User::USER_TYPE_STUDENT) {
                $user->sendSetPasswordMail();
            }
        } catch (\Exception $ex) {
            Log::error($ex);
            $error = "unable to insert user {$row[self::EMAIL]}";
            $this->errors[] = $error;
        }

    }

    private function validateRow($row, $rowNumber)
    {

        $request = new Request([
            'eid' => $row[self::EID],
            'wfmid' => $row[self::WFMID],
            'fname' => $row[self::FNAME],
            'lname' => $row[self::LNAME],
            'username' => $row[self::NAME],
            'password' => $row[self::PASSWORD],
            'country' => $row[self::COUNTRY],
            'department' => $row[self::DEPARTMENT],
            'email' => $row[self::EMAIL],
            'role' => $row[self::ROLE],
            'hub_name' => $row[self::HUB_NAME],
            'city_name' => $row[self::CITY_NAME]
        ]);

        $validator = Validator::make($request->all(), [
            'eid' => 'required|max:20|unique:users,employee_id',
            'wfmid' => ($this->type == User::USER_TYPE_STUDENT) ? 'required|max:20|unique:users,wfm_id' : 'nullable',
            'fname' => 'required',
            'lname' => 'required',
            'username' => 'required|unique:users,name',
            'password' => ($this->type == User::USER_TYPE_STUDENT) ? 'required|min:8|max:25' : 'nullable',
            'country' => 'required|exists:countries,code',
            'department' => ($this->type == User::USER_TYPE_STUDENT) ? 'required|exists:departments,name' : 'nullable',
            'email' => ($this->type == User::USER_TYPE_STUDENT) ? 'nullable' : 'required|email|unique:users',
            'role' => ($this->type == User::USER_TYPE_TEACHER) ? 'required|exists:roles,name' : 'nullable',
            'hub_name' => 'required',
            'city_name' => 'required',
        ]);

        if ($validator->fails()) {
            $error = "Invalid data for row {$rowNumber}, {$validator->errors()->first()}";
            $this->errors[] = $error;
            return false;
        }

        return true;
    }
}
