<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('manage delivery');
    }

    public function rules(): array
    {
        return [
            'project_id' => 'required|integer|exists:projects,id|unique:deliveries,project_id', // Satu proyek, satu pengiriman
            'shipping_address' => 'required|string',
        ];
    }
}
