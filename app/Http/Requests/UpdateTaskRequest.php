<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Vérifier que l'utilisateur est propriétaire du projet de la tâche
        $task = $this->route('task');
        return $task && $task->project->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:pending,in_progress,done',
            'deadline' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre de la tâche est obligatoire.',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut doit être: En attente, En cours ou Terminé.',
            'deadline.date' => 'La date limite doit être une date valide.',
        ];
    }
}