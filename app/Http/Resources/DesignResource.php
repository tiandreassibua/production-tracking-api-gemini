<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DesignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'design_id' => $this->id,
            'status' => $this->status,
            'initial_file_url' => $this->initial_file_path,
            'final_file_url' => $this->final_file_path,
            'assigned_at' => $this->created_at->format('d-m-Y H:i:s'),
            'project' => [
                'id' => $this->project->id,
                'name' => $this->project->name,
            ],
            'client' => [
                'name' => $this->project->client->name,
                'company' => $this->project->client->company_name,
            ]
        ];
    }
}
