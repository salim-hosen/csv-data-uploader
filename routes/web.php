<?php

use App\Http\Controllers\ContactController;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\Term;
use App\Models\TermTaxonomy;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [ContactController::class, 'index'])->name("welcome");

Route::get("/upload", [ContactController::class, 'showContactForm'])->name("contacts.upload");



Route::get("country", function(){

    \DB::beginTransaction();
    try{
        $countries = Country::all();
        foreach ($countries as $country) {

            $country = Term::create([
                "name" => $country->name,
                "slug" => Str::slug($country->name),
                "term_group" => 0,
            ]);

            $country_taxonomy = TermTaxonomy::create([
                "term_id" => $country->term_id,
                "taxonomy" => "property_country",
                "description" => "",
                "parent" => 0,
                "count" => 0,
            ]);
        }

        \DB::commit();
        echo "Done";
    }catch(\Exception $e){
        \DB::rollback();
    }

});



Route::get("state", function(){

    \DB::beginTransaction();
    try{

        $states = State::all();
        foreach ($states as $state) {

            $term = Term::create([
                "name" => $state->name,
                "slug" => Str::slug($state->name),
                "term_group" => 0,
                "lara_parent_id" => $state->country_id,
                "type" => "state"
            ]);

            $country_taxonomy = TermTaxonomy::create([
                "term_id" => $term->term_id,
                "taxonomy" => "property_state",
                "description" => "",
                "parent" => 0,
                "count" => 0,
            ]);
        }

        \DB::commit();
        echo "Done";
    }catch(\Exception $e){
        \DB::rollback();
        throw $e;
    }

});



Route::get("city", function(){

    \DB::beginTransaction();
    try{
        $cities = City::all();
        foreach ($cities as $city) {

            $term = Term::create([
                "name" => $city->name,
                "slug" => Str::slug($city->name),
                "term_group" => 0,
                "lara_parent_id" => $city->state_id,
                "type" => "city"
            ]);

            $country_taxonomy = TermTaxonomy::create([
                "term_id" => $term->term_id,
                "taxonomy" => "property_city",
                "description" => "",
                "parent" => 0,
                "count" => 0,
            ]);
        }

        \DB::commit();
        echo "Done";
    }catch(\Exception $e){
        \DB::rollback();
        throw $e;
    }

});



Route::get("area", function(){

    ini_set('max_execution_time', 360 ) ;
    \DB::beginTransaction();
    try{
        $areas = Area::all();

        $chunks = $areas->chunk(1000);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $area) {

                $term = Term::create([
                    "name" => $area->name,
                    "slug" => Str::slug($area->name),
                    "term_group" => 0,
                    "lara_parent_id" => $area->city_id,
                    "type" => "area"
                ]);

                $country_taxonomy = TermTaxonomy::create([
                    "term_id" => $term->term_id,
                    "taxonomy" => "property_area",
                    "description" => "",
                    "parent" => 0,
                    "count" => 0,
                ]);
            }
        }

        \DB::commit();
        echo "Done";
    }catch(\Exception $e){
        \DB::rollback();
    }

});
