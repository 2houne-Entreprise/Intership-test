<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|max:255',
            'status' => 'required|in:pending,in_progress,done',
            'deadline' => 'nullable|date',
            'attachment' => 'nullable|file|max:2048',
        ];
    }
}
