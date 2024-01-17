<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseContentAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_contents_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_course_contents_id')->references('id')->on('course_contents');
            $table->string('path', 255);
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
        Schema::dropIfExists('course_contents_attachments');
    }
}
