<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules() {
    return [
        'title'=>'required|max:255',
        'status'=>'required|in:pending,in_progress,done',
        'deadline'=>'nullable|date',
        'attachment'=>'nullable|file'
    ];
}
}
