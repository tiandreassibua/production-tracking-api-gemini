<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Hanya user dengan izin 'edit project' yang boleh mengakses
        return auth()->user()->can('edit project');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'sometimes' berarti validasi hanya berjalan jika field-nya ada di request.
            // Ini penting untuk update, karena user mungkin hanya ingin mengubah nama saja,
            // tanpa mengirim field lainnya.
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'client_id' => 'sometimes|required|integer|exists:clients,id',
            'due_date' => 'sometimes|nullable|date',
            'status' => 'sometimes|required|string|in:negotiation,pending,in_progress,quality_check,delivery,completed,cancelled' // Batasi nilai status
        ];
    }
}
