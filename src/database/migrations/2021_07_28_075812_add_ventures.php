<?php

use App\Models\Country;
use Illuminate\Database\Migrations\Migration;

class AddVentures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $country = new Country();
        $country->code = "PAK";
        $country->name = "Pakistan";
        $country->save();

        $country = new Country();
        $country->code = "BGD";
        $country->name = "Bangladesh";
        $country->save();

        $country = new Country();
        $country->code = "NPL";
        $country->name = "Nepal";
        $country->save();

        $country = new Country();
        $country->code = "LKA";
        $country->name = "Sri Lanka";
        $country->save();

        $country = new Country();
        $country->code = "MMR";
        $country->name = "Myanmar";
        $country->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $countries = Country::whereIn('code', ["PAK", "BGD", "NPL", "LKA", "MMR"])->get();

        foreach ($countries as $country) {
            $country->forceDelete();
        }
    }
}
