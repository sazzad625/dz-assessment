<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateQuestionsBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions_bank', function (Blueprint $table) {
            $table->dropUnique('uk_name');
            $table->index('name', 'idx_name');
            $table->unsignedBigInteger('fk_quiz_id')->after('fk_course_categories_id');
            $table->foreign('fk_quiz_id', 'foreign_quiz_id')->references('id')->on('quizzes');
        });

        Schema::rename('questions_bank', 'quiz_questions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropIndex('idx_name');
            $table->unique('name', 'uk_name');
            $table->dropForeign('foreign_quiz_id');
            $table->dropColumn('fk_quiz_id');
        });

        Schema::rename('quiz_questions', 'questions_bank');
    }
}
