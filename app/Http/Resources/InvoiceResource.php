<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'status' => $this->status,
            'description' => $this->description,
            'amount' => number_format($this->amount, 2, ',', '.'),
            'due_date' => $this->due_date,
            'payment_proof_url' => $this->payment_proof_path,
            'paid_at' => $this->paid_at,
            'project' => [
                'id' => $this->project->id,
                'name' => $this->project->name,
            ],
            'issued_by' => [
                'id' => $this->finance->id,
                'name' => $this->finance->name,
            ],
        ];
    }
}
