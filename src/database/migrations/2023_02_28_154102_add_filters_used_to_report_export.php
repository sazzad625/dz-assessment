<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFiltersUsedToReportExport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_export', function (Blueprint $table) {
            //It will show the values used for filtering and generating the file
            $table->string('filters_used',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_export', function (Blueprint $table) {
            //
            $table-> dropColumn('filters_used');
        });
    }
}
