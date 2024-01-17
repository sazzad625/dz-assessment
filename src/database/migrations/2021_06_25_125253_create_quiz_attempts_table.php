<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_user_id')->references('id')->on('users');
            $table->foreignId('fk_quiz_id')->references('id')->on('quizzes');
            $table->foreignId('fk_quiz_grading_type_id')->references('id')
                ->on('quiz_grading_types');
            $table->integer('total_attempts');
            $table->integer('grading_percentage');
            $table->enum('grading_final_result', ['PASS', 'FAILED'])->index('idx_final_result')
                ->nullable();
            $table->dateTime('last_attempt_time')->index('idx_last_attempt_time');
            $table->timestamps();
            $table->softDeletes();
            $table->index('deleted_at', 'idx_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_attempts');
    }
}
