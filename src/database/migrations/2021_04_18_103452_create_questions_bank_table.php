<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_course_categories_id')->references('id')->on('course_categories');
            $table->foreignId('fk_question_types_id')->references('id')->on('question_types');
            $table->foreignId('created_by')->references('id')->on('users');
            $table->char('name', 255)->unique('uk_name');
            $table->json('question');
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
        Schema::dropIfExists('questions_bank');
    }
}
