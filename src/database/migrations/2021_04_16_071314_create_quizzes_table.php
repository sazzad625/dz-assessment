<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_course_id')->references('id')->on('courses');
            $table->char('name', 125)->index('idx_name');
            $table->string('description', 255);
            $table->dateTime('start_time')->nullable()->index('idx_start_time');
            $table->dateTime('end_time')->nullable()->index('idx_end_time');
            $table->integer('time_limit')->nullable()->index('idx_time_limit');
            $table->integer('passing_percentage');
            $table->integer('attempts_allowed')->default(1);
            $table->boolean('shuffle')->default(false);
            $table->integer('max_questions');
            $table->boolean('is_active')->index('idx_is_active');
            $table->timestamps();
            $table->softDeletes();

            $table->index('deleted_at',  'idx_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quizzes');
    }
}
