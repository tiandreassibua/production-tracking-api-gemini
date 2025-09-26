<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Hanya user yang punya izin 'create project' yang boleh melanjutkan.
        // Ini adalah kekuatan dari Spatie!
        return auth()->user()->can('create project');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'required|integer|exists:clients,id', // Pastikan client_id ada di tabel clients
            'due_date' => 'nullable|date',
        ];
    }
}
