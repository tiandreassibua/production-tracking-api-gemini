<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'shipping_address' => $this->shipping_address,
            'handover_document_url' => $this->handover_document_path,
            'delivered_at' => $this->delivered_at,
            'project_name' => $this->project->name,
            'pic' => $this->ppic->name,
        ];
    }
}
