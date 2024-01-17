<?php

use App\Models\ReportExport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportExportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_export', function (Blueprint $table) {
            $table->id();
            $table->string('path', 255)->nullable();
            $table->char('type', 50)->index('idx_type');
            $table->enum('status', [ReportExport::STATUS_QUEUED, ReportExport::STATUS_IN_PROGRESS,
                ReportExport::STATUS_FAILED, ReportExport::STATUS_COMPLETE]);
            $table->foreignId('fk_user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_export');
    }
}
