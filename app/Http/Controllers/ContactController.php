<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadContactCsvRequest;
use App\Http\Resources\UploadSummaryResource;
use Illuminate\Http\Request;
use App\Jobs\ContactCsvProcess;
use App\Models\Contact;
use App\Models\UploadSummary;
use Illuminate\Support\Facades\Bus;
use DB;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::orderBy("created_at", "desc");

        $filter_by = request("filter_by");
        $query = request("q");

        if($filter_by){
            $contacts = $contacts->where($filter_by, "like", "%$query%");
        }else{
            $contacts = $contacts->where("name", "like", "%$query%")
                            ->orWhere("email", "like", "%$query%")
                            ->orWhere("phone_number", "like", "%$query%")
                            ->orWhere("gender", "like", "%$query%")
                            ->orWhere("address", "like", "%$query%");
        }

        $contacts = $contacts->paginate(request("per_page", 20))->appends(request()->query());

        return view('welcome', compact("contacts"));
    }

    public function showContactForm()
    {
        return view('upload');
    }

    public function store(UploadContactCsvRequest $request)
    {

        if ($request->hasFile('csv')) {

            $data   =   file($request->csv);

            // Chunking file
            $chunks = array_chunk($data, 1000);

            $batch = Bus::batch([])->dispatch();

            foreach ($chunks as $key => $chunk) {
                $data = array_map('str_getcsv', $chunk);

                if ($key === 0) {
                    unset($data[0]);
                }

                $batch->add(new ContactCsvProcess($data));
            }

            return $batch;
        }

        return response([
            "success" => false,
            "message" => "Something Wen't Wrong"
        ], 422);
    }

    public function getUploadDetails($batch_id)
    {
        $batch = DB::table('job_batches')->where("id", $batch_id)->first();
        if(!$batch) abort(404, "Not Found");
        return Bus::findBatch($batch_id);
    }

    public function deleteJob($batch_id){

        $batch = DB::table('job_batches')->where("id", $batch_id)->first();
        $batch->delete();

        return true;
    }

    public function getUploadSummary($batch_id)
    {
        $upload_summary = UploadSummary::where("job_batch_id", $batch_id)->first();
        return new UploadSummaryResource($upload_summary);
    }
}
