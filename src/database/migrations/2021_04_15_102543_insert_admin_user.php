<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertAdminUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $product_ops           = new User();
        $product_ops->name     = 'ProductOps';
        $product_ops->email    = env("ADMIN_EMAIL", 'productops@daraz.pk');
        $product_ops->type    = 'ADMIN';
        $product_ops->password = bcrypt(env("ADMIN_PASSWORD", 'secret'));
        $product_ops->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
