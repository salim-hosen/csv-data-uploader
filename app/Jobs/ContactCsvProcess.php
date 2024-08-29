<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Models\Term;
use App\Models\TermTaxonomy;
use App\Models\UploadSummary;
use Throwable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// DELETE FROM `qwcheq_options` WHERE option_name like "%_houzez_property_country%";
// DELETE FROM `qwcheq_options` WHERE option_name like "%_houzez_property_state%";
// DELETE FROM `qwcheq_options` WHERE option_name like "%_houzez_property_city%";
// DELETE FROM `qwcheq_options` WHERE option_name like "%_houzez_property_area%"

class ContactCsvProcess implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data   = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $total_data = 0;
        $total_successful = 0;
        $total_duplicate = 0;
        $total_invalid = 0;
        $total_incomplete = 0;

        foreach ($this->data as $contact) {

            $total_data++;

            $area_name = $contact[0];
            $city_name = $contact[1];
            $state_name = $contact[2];
            $country_name = $contact[3];

            // check if all fields are present
            if(!$area_name || !$city_name || !$state_name || !$country_name){
                $total_incomplete++;
                continue;
            }

            $area_name = trim($area_name);
            $city_name = trim($city_name);
            $state_name = trim($state_name);
            $country_name = trim($country_name);


            // check country
            $country = Term::where("name", $country_name)->first();
            $country_taxonomy = TermTaxonomy::where('term_id', $country?->term_id)->first();

            if(!$country){
                $country = Term::create([
                    "name" => $country_name,
                    "slug" => Str::slug($country_name),
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


            $state = Term::where("name", $state_name)->first();
            $state_taxonomy = TermTaxonomy::where('term_id', $state?->term_id)->first();

            if($state && $state_taxonomy && $country->term_id == $state->parent){
                $state = false;
            }

            if(!$state){
                $state = Term::create([
                    "name" => $state_name,
                    "slug" => Str::slug($state_name),
                    "term_group" => 0,
                ]);

                $state_taxonomy = TermTaxonomy::create([
                    "term_id" => $state->term_id,
                    "taxonomy" => "property_state",
                    "description" => "",
                    "parent" => $country->term_id ?? 0,
                    "count" => 0,
                ]);

                // $option = '_houzez_property_state_' . $state->term_id;
                // $parent_key = "parent_country";
                // $parent_value = $country->slug;

                // $response = Http::get('http://ant.test/wp-json/laravel/v1/update-option', [
                //     'option' => $option,
                //     'parent_key' => $parent_key,
                //     'parent_value' => $parent_value,
                // ]);

                // Log::info($response->body());
            }

            $city = Term::where("name", $city_name)->first();
            $city_taxonomy = TermTaxonomy::where('term_id', $city?->term_id)->first();

            if($city && $city_taxonomy && $state->term_id == $city->parent){
                $city = false;
            }

            if(!$city){
                $city = Term::create([
                    "name" => $city_name,
                    "slug" => Str::slug($city_name),
                    "term_group" => 0,
                ]);

                $city_taxonomy = TermTaxonomy::create([
                    "term_id" => $city->term_id,
                    "taxonomy" => "property_city",
                    "description" => "",
                    "parent" => $state->term_id ?? 0,
                    "count" => 0,
                ]);

                // $option = '_houzez_property_city_' . $city->term_id;
                // $parent_key = "parent_state";
                // $parent_value = $state->slug;

                // $response = Http::get('http://ant.test/wp-json/laravel/v1/update-option', [
                //     'option' => $option,
                //     'parent_key' => $parent_key,
                //     'parent_value' => $parent_value,
                // ]);

                // Log::info($response->body());
            }


            $area = Term::where("name", $area_name)->first();
            $area_taxonomy = TermTaxonomy::where('term_id', $area?->term_id)->first();

            if($area && $area_taxonomy && $city->term_id == $area->parent){
                $area = false;
            }

            if(!$area){
                $area = Term::create([
                    "name" => $area_name,
                    "slug" => Str::slug($area_name),
                    "term_group" => 0,
                ]);

                $area_taxonomy = TermTaxonomy::create([
                    "term_id" => $area->term_id,
                    "taxonomy" => "property_area",
                    "description" => "",
                    "parent" => $area->term_id ?? 0,
                    "count" => 0,
                ]);

                // $option = '_houzez_property_area_' . $area->term_id;
                // $parent_key = "parent_city";
                // $parent_value = $city->slug;

                // $response = Http::get('http://ant.test/wp-json/laravel/v1/update-option', [
                //     'option' => $option,
                //     'parent_key' => $parent_key,
                //     'parent_value' => $parent_value,
                // ]);

                // Log::info($response->body());

            }

            $total_successful++;
        }

        $batch_id = $this->batchId;
        $upload_summary = UploadSummary::where("job_batch_id", $batch_id)->first();
        if(!$upload_summary){
            $upload_summary = new UploadSummary();
            $upload_summary->job_batch_id = $batch_id;
            // $upload_summary->save();
        }
        $upload_summary->total_data += $total_data;
        $upload_summary->total_successful += $total_successful;
        $upload_summary->total_duplicate += $total_duplicate;
        $upload_summary->total_invalid += $total_invalid;
        $upload_summary->total_incomplete += $total_incomplete;
        $upload_summary->save();
    }

    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
    }
}
