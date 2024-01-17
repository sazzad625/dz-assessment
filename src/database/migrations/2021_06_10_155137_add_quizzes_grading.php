<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuizzesGrading extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('fk_quiz_grading_type_id')->after('is_active')->references('id')->on('quiz_grading_types');
            $table->boolean('randomize')->after('shuffle')->default(false);
            $table->boolean('allow_review')->after('randomize')->default(false);
            $table->enum('type', ['GRADED', 'UNGRADED'])->after('randomize');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('randomize');
            $table->dropColumn('allow_review');
            $table->dropColumn('type');
            $table->dropConstrainedForeignId('fk_quiz_grading_type_id');
        });
    }
}
