<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $country = new Country();
        $country->code = "PAK";
        $country->name = "Pakistan";
        $country->save();

        $country = new Country();
        $country->code = "BGD";
        $country->name = "Bangladesh";
        $country->save();
    }
}
