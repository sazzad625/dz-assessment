<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_course_categories_id')->references('id')->on('course_categories');
            $table->char('short_name', 20)->unique('uk_short_name');
            $table->char('name', 125)->index('idx_name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->index('idx_is_active');
            $table->foreignId('fk_created_by')->references('id')->on('users');
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
        Schema::dropIfExists('courses');
    }
}
