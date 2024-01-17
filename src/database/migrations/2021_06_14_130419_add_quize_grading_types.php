<?php

use App\Models\QuizGradingType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuizeGradingTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $type = new QuizGradingType();
        $type->code_name = 'highest';
        $type->name = 'Highest';
        $type->save();

        $type = new QuizGradingType();
        $type->code_name = 'average';
        $type->name = 'Average';
        $type->save();

        $type = new QuizGradingType();
        $type->code_name = 'first_attempt';
        $type->name = 'First Attempt';
        $type->save();

        $type = new QuizGradingType();
        $type->code_name = 'last_attempt';
        $type->name = 'Last Attempt';
        $type->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        QuizGradingType::where('code_name', 'highest')->first()->delete();
        QuizGradingType::where('code_name', 'average')->first()->delete();
        QuizGradingType::where('code_name', 'first_attempt')->first()->delete();
        QuizGradingType::where('code_name', 'last_attempt')->first()->delete();
    }
}
