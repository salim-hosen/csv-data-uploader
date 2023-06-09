<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UploadSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "total_data" => $this->total_data,
            "total_successful" => $this->total_successful,
            "total_duplicate" => $this->total_duplicate,
            "total_invalid" => $this->total_invalid,
            "total_incomplete" => $this->total_incomplete,
        ];
    }
}
