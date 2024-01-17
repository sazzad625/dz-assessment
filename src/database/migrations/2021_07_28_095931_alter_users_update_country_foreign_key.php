<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersUpdateCountryForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_fk_country_id_foreign');
            $table->foreign('fk_country_id', 'users_fk_country_id_foreign')
                ->references('id')->on('countries');
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
            $table->dropForeign('users_fk_country_id_foreign');
            $table->foreign('fk_country_id', 'users_fk_country_id_foreign')
                ->references('id')->on('users');
        });
    }
}
