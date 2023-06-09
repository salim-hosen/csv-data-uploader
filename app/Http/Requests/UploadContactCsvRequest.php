<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadContactCsvRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "csv" => ["required", "mimes:csv,txt"]
        ];
    }

    public function messages()
    {
        return [
            'csv.required' => 'Please Upload a CSV File',
            'csv.mimes' => 'You CSV file is Invalid',
        ];
    }
}
