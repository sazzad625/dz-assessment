<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizAttemptsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_attempts_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_quiz_attempt_id')->references('id')->on('quiz_attempts');
            $table->dateTime('start_time')->index('idx_start_time');
            $table->dateTime('end_time')->index('idx_end_time')->nullable();
            $table->dateTime('expected_end_time')->index('idx_expected_end_time')->nullable();
            $table->enum('result', ['PASS', 'FAILED'])->index('idx_final_result')->nullable();
            $table->enum('status', ['INITIATED', 'COMPLETED'])->index('idx_status')
                ->default('INITIATED');
            $table->integer('percentage')->nullable();
            $table->json('quiz_given');
            $table->json('quiz_attempt');
            $table->timestamps();
            $table->softDeletes()->index('idx_deleted_at');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_attempts_details');
    }
}
