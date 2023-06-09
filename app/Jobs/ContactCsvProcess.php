<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Models\UploadSummary;
use Throwable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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

            $name = $contact[0];
            $email = $contact[1];
            $phone = $contact[2];
            $gender = $contact[3];
            $address = $contact[4];

            // check if all fields are present
            if(!$name || !$email || !$phone || !$gender || !$address){
                $total_incomplete++;
                continue;
            }

            // phone validation
            $pattern = "/^(?:\+88|88)?(01[3-9]\d{8})$/";
            if(!preg_match($pattern, $phone)){
                $total_invalid++;
                continue;
            }

            // phone unique check
            $phone_exists = Contact::where("phone_number", $phone)->first();
            if($phone_exists){
                $total_duplicate++;
                continue;
            }

            // email validation
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $total_invalid++;
                continue;
            }
            else{
                list($username, $domain) = explode('@', $email);
                if (!checkdnsrr($domain, 'MX')) {
                    $total_invalid++;
                    continue;
                }
            }

            // email unique check
            $email_exists = Contact::where("email", $email)->first();
            if($email_exists) {
                $total_duplicate++;
                continue;
            }



            Contact::create([
                "name" => $name,
                "email" => $email,
                "phone_number" => $phone,
                "gender" => $gender,
                "address" => $address,
            ]);

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
