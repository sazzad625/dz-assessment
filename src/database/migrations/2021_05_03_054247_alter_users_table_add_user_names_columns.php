<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterUsersTableAddUserNamesColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 50)->after('id')->nullable();
            $table->string('last_name', 50)->after('first_name')->nullable();
            DB::statement('alter table users change `name` `name` char(60) not null'); //using custom query because doctrine does not support it
            $table->unique('name', 'uk_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
            $table->dropUnique('uk_name');
            $table->string('name', 255)->change();
        });
    }
}
