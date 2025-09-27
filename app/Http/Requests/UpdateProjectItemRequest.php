<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Cek apakah user punya izin 'update project progress'
        return auth()->user()->can('update project progress');
    }

    public function rules(): array
    {
        return [
            'progress' => 'sometimes|integer|min:0|max:100',
            'status' => 'sometimes|string|max:255',
        ];
    }
}
