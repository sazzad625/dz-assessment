<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAclTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id('id');
            $table->char('name', 50)->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id('id');
            $table->char('name', 50)->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignId('fk_role_id')->references('id')->on('roles');
            $table->foreignId('fk_permission_id')->references('id')->on('permissions');

            //SETTING THE PRIMARY KEYS
            $table->unique(['fk_role_id', 'fk_permission_id']);
        });

        Schema::create('user_role', function (Blueprint $table) {
            $table->foreignId('fk_user_id')->references('id')->on('users');
            $table->foreignId('fk_role_id')->references('id')->on('roles');

            //SETTING THE PRIMARY KEYS
            $table->unique(['fk_user_id', 'fk_role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
}