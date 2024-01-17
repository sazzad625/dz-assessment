<?php

use Alexusmai\LaravelFileManager\Services\ConfigService\ConfigRepository;
use App\Helpers\PathHelper;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Category\CategoryViewController;
use App\Http\Controllers\Course\CourseContentController;
use App\Http\Controllers\Course\CourseViewController;
use App\Http\Controllers\Course\CourseParticipantController;
use App\Http\Controllers\Department\DepartmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OSSStorageController;
use App\Http\Controllers\Quiz\QuizAttemptController;
use App\Http\Controllers\Quiz\QuizController;
use App\Http\Controllers\Quiz\QuizReviewController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Report\IndividualPerformanceReportController;
use App\Http\Controllers\Report\CoursePerformanceReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes([
    'register' => false
]);

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/course-count', [HomeController::class, 'getCourseCount'])->name('home.get-course-count');
    Route::get('/category-count', [HomeController::class, 'getCourseCategoryCount'])->name('home.get-category-count');
    Route::get('/student-count', [HomeController::class, 'getStudentCount'])->name('home.get-student-count');
    Route::get('/teacher-count', [HomeController::class, 'getTeacherCount'])->name('home.get-teacher-count');
    Route::get('/attempted-quiz-count', [HomeController::class, 'getAttemptedQuizCount'])->name('home.get-attempted-quiz-count');

    //Roles & Permission Crud
    Route::get("/role", [RoleController::class, 'view'])->name("role.view");
    Route::get("/role/create", [RoleController::class, 'create'])->name("role.create");
    Route::post("/role/create", [RoleController::class, 'handleCreate'])->name("role.create");
    Route::get("/role/update/{id}", [RoleController::class, 'update'])->name("role.update");
    Route::post("/role/update/{id}", [RoleController::class, 'handleUpdate'])->name("role.update");
    Route::get("/role/fetchroles", [RoleController::class, 'fetchRoles'])->name("role.fetchRoles");

    Route::get('/category/add',
        [CategoryController::class, 'addCourseCategory'])->name('category.add');
    Route::post('/category/add',
        [CategoryController::class, 'handleAddCourseCategory'])->name('category.add');
    Route::get('/category/update/{id}',
        [CategoryController::class, 'updateCourseCategory'])->name('category.update');
    Route::post('/category/update/{id}',
        [CategoryController::class, 'handleUpdateCourseCategory'])->name('category.update');
    Route::post('/category/delete',
        [CategoryController::class, 'deleteCourseCategory'])->name('category.delete');
    Route::get('/category/view',
        [CategoryController::class, 'index'])->name('category.view');

    Route::get('/course/add',
        [CourseController::class, 'addCourse'])->name('course.add');
    Route::post('/course/add',
        [CourseController::class, 'handleAddCourse'])->name('course.add');
    Route::get('/course/update/{id}',
        [CourseController::class, 'updateCourse'])->name('course.update');
    Route::post('/course/update/{id}',
        [CourseController::class, 'handleUpdateCourse'])->name('course.update');
    Route::get('/course/search',
        [CourseController::class, 'search'])->name('course.search');
    Route::get('/course/{course}/clone',
        [CourseController::class, 'clone'])->name('course.clone');
    Route::post('/course/delete',
        [CourseController::class, 'delete'])->name('course.delete');

    Route::get('/storage/{uri}', [OSSStorageController::class, 'getPublicAssets'])->where('uri',
        PathHelper::getPublicPath() . '.*')->name('public.storage');

    Route::get('/storage-secure/{uri}', [OSSStorageController::class, 'getSecureAssets'])->where('uri',
        PathHelper::getStoragePath() . '.*')->name('secure.storage');

    Route::get('/category', [CategoryViewController::class, 'index'])->name('category');

    Route::get('/course', [CourseViewController::class, 'index'])->name('course');
    Route::get('/course/category/{id}', [CourseViewController::class, 'index'])->name('course.category');


    Route::get('/user/add/{type}',
        [UserController::class, 'index'])->name('user.add');

    Route::post('/user/add/{type}',
        [UserController::class, 'handleAddUser'])->name('user.add');

    Route::get('/user/update/type/{type}/id/{id}',
        [UserController::class, 'updateUser'])->name('user.update');

    Route::post('/user/update/type/{type}/id/{id}',
        [UserController::class, 'handleUpdateUser'])->name('user.update');

    Route::get('/user/search/{type}',
        [UserController::class, 'search'])->name('user.search');
    Route::get('/user/search/{type}/ajax',
        [UserController::class, 'searchAjax'])->name('user.search.ajax');

    Route::post('/user/delete/{type}',
        [UserController::class, 'delete'])->name('user.delete');

    Route::get('/user/upload/template-download',
        [UserController::class, 'downloadStudentTemplate'])->name('user.upload.template.download');

    Route::get('/user/upload/{type}', [UserController::class, 'bulkUploadUser'])->name('user.upload');
    Route::post('/user/upload/{type}', [UserController::class, 'handleBulkUploadUser'])->name('user.upload');
    Route::post('/user/reset-password/id/{id}', [UserController::class, 'resetPasswordUser'])->name('user.reset.password');

    Route::get('/department/add',
        [DepartmentController::class, 'addDepartment'])->name('department.add');
    Route::post('/department/add',
        [DepartmentController::class, 'handleAddDepartment'])->name('department.add');
    Route::get('/department/update/{id}',
        [DepartmentController::class, 'updateDepartment'])->name('department.update');
    Route::post('/department/update/{id}',
        [DepartmentController::class, 'handleUpdateDepartment'])->name('department.update');
    Route::post('/department/delete',
        [DepartmentController::class, 'deleteDepartment'])->name('department.delete');
    Route::get('/department/search',
        [DepartmentController::class, 'search'])->name('department.search');
    Route::get('/course/{id}/participant/search',
        [CourseParticipantController::class, 'search'])->name('course.participant.search');
    Route::post('/course/{id}/participant/enroll',
        [CourseParticipantController::class, 'enroll'])->name('course.participant.enroll');
    Route::post('/course/{id}/participant/upload',
        [CourseParticipantController::class, 'bulkUploadCourseParticipant'])->name('course.participant.upload');
    Route::get('/course/participant/template-download',
        [CourseParticipantController::class, 'downloadParticipantTemplate'])->name('course.participant.template');
    Route::post('/course/{id}/participant/remove',
        [CourseParticipantController::class, 'remove'])->name('course.participant.remove');
    Route::post('/content', function () {
        return view('course.content');
    });

    Route::get('/course/{courseId}/content', [CourseContentController::class, 'manage'])->name('course.content.manage');
    Route::post('/course/{courseId}/get-content/{contentId}', [CourseContentController::class, 'getContent'])->name('course.content.get');
    Route::post('/course/{courseId}/save-content/{contentId}', [CourseContentController::class, 'saveContent'])->name('course.content.save');
    Route::post('/course/{courseId}/get-content-attachments/{contentId}', [CourseContentController::class, 'getContentAttachments'])->name('course.content.attachments.get');
    Route::post('/course/{courseId}/content/{contentId}/attachment/delete', [CourseContentController::class, 'deleteContentAttachment'])->name('course.content.attachment.delete');
    Route::post('/course/{courseId}/content/{contentId}/attachment/add', [CourseContentController::class, 'addContentAttachment'])->name('course.content.attachment.add');

    Route::get('/report/individual-performance-report', [IndividualPerformanceReportController::class, 'index'])
        ->name('report.individual-performance');
    Route::get('/report/individual-performance-report/student/{id}', [IndividualPerformanceReportController::class, 'student'])
        ->name('report.individual-student-performance');
    Route::post('/report/individual-performance-report/generate-file', [IndividualPerformanceReportController::class, 'generateFile'])
        ->name('report.individual-performance.generate-file');
    Route::post('/report/individual-performance-report/generate-file-for-all-records', [IndividualPerformanceReportController::class, 'generateFileForAllRecords'])
        ->name('report.individual-performance.generate-file-for-all-records');
    Route::get('/report/individual-performance-report/get-generate-files', [IndividualPerformanceReportController::class, 'getGenerateFiles'])
        ->name('report.individual-performance.get-generate-file');
    Route::get('/report/individual-performance-report/download/{path}', [IndividualPerformanceReportController::class, 'download'])
        ->where('path', PathHelper::getTempPath() . '.*')
        ->name('report.individual-performance.download');

    Route::get('/course/{id}', [CourseViewController::class, 'view'])->name('course.view');
    Route::get('/course/{courseId}/quiz/search', [QuizController::class, 'search'])->name('quiz.search');
    Route::post('/course/quiz/{quizId}/active-inactive', [QuizController::class, 'activeInactive'])->name('quiz.active-inactive');
    Route::get('/course/{courseId}/quiz/add', [QuizController::class, 'addQuiz'])->name('quiz.add');
    Route::post('/course/{courseId}/quiz/add', [QuizController::class, 'handleAddQuiz'])->name('quiz.add');
    Route::get('/course/quiz/update/{quizId}', [QuizController::class, 'updateQuiz'])->name('quiz.update');
    Route::post('/course/quiz/update/{quizId}', [QuizController::class, 'handleUpdateQuiz'])->name('quiz.update');
    Route::post("/quiz/fetch/{id}", [QuizController::class, 'fetchQuiz'])->name('quiz.fetch');

    Route::post('/quiz/{quizId}/questions/fetch', [QuizController::class, 'fetchQuizQuestions'])->name('quiz.questions.fetch');
    Route::post('/quiz/{quizId}/question/add', [QuizController::class, 'handleCreateQuestion'])->name('quiz.question.add');
    Route::post('/quiz/question/{questionId}/update', [QuizController::class, 'handleUpdateQuestion'])->name('quiz.question.update');
    Route::post('/quiz/question/{questionId}/delete', [QuizController::class, 'handleDeleteQuestion'])->name('quiz.question.delete');

    Route::get('/course-content/quiz-student', function () {
        return view('course-content.quiz-student');
    });

    //Total Attempts
    Route::get('/report/course-performance-report/export/{courseId}/{quiz_id}', [CoursePerformanceReportController::class, 'export'])
        ->name('export');

    Route::post('/report/course-performance-report/generate-file', [CoursePerformanceReportController::class, 'generateFile'])
        ->name('report.course-performance.generate-file');
    Route::post('/report/course-performance-report/generate-file-for-all-records', [CoursePerformanceReportController::class, 'generateFileForAllRecords'])
        ->name('report.course-performance.generate-file-for-all-records');
    Route::get('/report/course-performance-report/get-generate-files', [CoursePerformanceReportController::class, 'getGeneratedFilesList'])
        ->name('report.course-performance.get-generate-file');
    Route::get('/report/course-performance-report/{courseId?}', [CoursePerformanceReportController::class, 'index'])
        ->name('report.course-performance');
    Route::get('/report/download/{id}', [OSSStorageController::class, 'reportDownload'])
        ->name('report.course-performance.download');
    Route::get('/report/course-performance-report/get-quiz-analytics/{courseId?}/{quizId?}', [CoursePerformanceReportController::class, 'quizAnalytics'])
        ->name('report.course-performance.get-quiz-analytics');

    Route::get('/quiz/last-attempt/results/{quizId}', [QuizAttemptController::class, 'quizResults'])->name('quiz.last.attempt.result');
    Route::get('/quiz/attempt/results/{quizId}', [QuizAttemptController::class, 'finish'])->name('quiz.landing');
    Route::post('/quiz/{quizId}/initiate', [QuizAttemptController::class, 'initiate'])->name('quiz.initiate');
    Route::get('/quiz/attempt/{attemptDetailId}', [QuizAttemptController::class, 'attempt'])->name('quiz.attempt');
    Route::post('/quiz/submit/{attemptDetailId}', [QuizAttemptController::class, 'submit'])->name('quiz.submit');

    Route::get('/quiz/review/{courseId}/{userId}', [QuizReviewController::class, 'index'])->name('quiz.review.list');
    Route::get('/quiz/review/{quizDetailId}', [QuizReviewController::class, 'review'])->name('quiz.review');


});

$config = resolve(ConfigRepository::class);

// App middleware list
$middleware = $config->getMiddleware();
$middleware[] = 'auth';

/**
 * If ACL ON add "fm-acl" middleware to array
 */
if ($config->getAcl()) {
    $middleware[] = 'fm-acl';
}

Route::group([
    'middleware' => $middleware,
    'prefix' => $config->getRoutePrefix(),
    'namespace' => 'App\Http\Controllers\FileManager',
], function () {

    Route::get('initialize', 'FileManagerController@initialize')
        ->name('fm.initialize');

    Route::get('content', 'FileManagerController@content')
        ->name('fm.content');

    Route::get('tree', 'FileManagerController@tree')
        ->name('fm.tree');

    Route::get('select-disk', 'FileManagerController@selectDisk')
        ->name('fm.select-disk');

    Route::post('upload', 'FileManagerController@upload')
        ->name('fm.upload');

    Route::post('delete', 'FileManagerController@delete')
        ->name('fm.delete');

    Route::post('paste', 'FileManagerController@paste')
        ->name('fm.paste');

    Route::post('rename', 'FileManagerController@rename')
        ->name('fm.rename');

    Route::get('download', 'FileManagerController@download')
        ->name('fm.download');

    Route::get('thumbnails', 'FileManagerController@thumbnails')
        ->name('fm.thumbnails');

    Route::get('preview', 'FileManagerController@preview')
        ->name('fm.preview');

    Route::get('url', 'FileManagerController@url')
        ->name('fm.url');

    Route::post('create-directory', 'FileManagerController@createDirectory')
        ->name('fm.create-directory');

    Route::post('create-file', 'FileManagerController@createFile')
        ->name('fm.create-file');

    Route::post('update-file', 'FileManagerController@updateFile')
        ->name('fm.update-file');

    Route::get('stream-file', 'FileManagerController@streamFile')
        ->name('fm.stream-file');

    Route::post('zip', 'FileManagerController@zip')
        ->name('fm.zip');

    Route::post('unzip', 'FileManagerController@unzip')
        ->name('fm.unzip');

    // Integration with editors
    Route::get('ckeditor', 'FileManagerController@ckeditor')
        ->name('fm.ckeditor');

    Route::get('tinymce', 'FileManagerController@tinymce')
        ->name('fm.tinymce');

    Route::get('tinymce5', 'FileManagerController@tinymce5')
        ->name('fm.tinymce5');

    Route::get('summernote', 'FileManagerController@summernote')
        ->name('fm.summernote');

    Route::get('fm-button', 'FileManagerController@fmButton')
        ->name('fm.fm-button');
});

