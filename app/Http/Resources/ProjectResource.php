<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'due_date' => $this->due_date,
            // Kita akan mengambil data dari relasi yang sudah kita buat
            'client' => [
                'id' => $this->client->id,
                'name' => $this->client->name,
                'company' => $this->client->company_name,
            ],
            'marketing_in_charge' => [
                'id' => $this->marketing->id,
                'name' => $this->marketing->name,
            ],
            'created_at' => $this->created_at->format('d-m-Y H:i:s'),
        ];
    }
}
