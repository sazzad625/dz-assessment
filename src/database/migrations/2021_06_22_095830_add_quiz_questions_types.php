<?php

use App\Models\QuestionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuizQuestionsTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $question = new QuestionType();
        $question->name = QuestionType::TYPE_MULTI_CHOICE;
        $question->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        QuestionType::where('name', QuestionType::TYPE_MULTI_CHOICE)->first()->forceDelete();

    }
}
