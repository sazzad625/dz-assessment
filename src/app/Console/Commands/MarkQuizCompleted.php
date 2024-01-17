<?php

namespace App\Console\Commands;

use App\Helpers\Carbon;
use App\Models\QuizAttemptDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MarkQuizCompleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mark-quiz-complete {minutes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark quiz completed';

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
     * @return int
     */
    public function handle()
    {
        $minutes = $this->argument('minutes');
        Log::info("Started cron mark quiz completed with buffer {$minutes} minutes");
        QuizAttemptDetail::where('status', QuizAttemptDetail::STATUS_INITIATED)
            ->whereNotNull('expected_end_time')
            ->where('expected_end_time', '<', Carbon::now()->subMinutes($minutes))
            ->chunk(200, function ($quizAttemptDetails) {
                foreach ($quizAttemptDetails as $quizAttemptDetail) {
                    Log::info("Performing operation", ['quiz_detail_id' => $quizAttemptDetail->id]);
                    $quizAttemptDetail->saveCalculatedResult();
                    $quizAttemptDetail->attempt->saveAggregatedData();

                    $quizAttemptDetail->status = QuizAttemptDetail::STATUS_COMPLETED;
                    $quizAttemptDetail->end_time = Carbon::now();
                    $quizAttemptDetail->save();
                }
            });
        Log::info("Mark quiz completed cron ended");

        return 0;
    }
}
