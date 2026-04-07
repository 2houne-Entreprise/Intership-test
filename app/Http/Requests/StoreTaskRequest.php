<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Vérifier que l'utilisateur est propriétaire du projet
        $project = $this->route('project');
        return $project && $project->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,in_progress,done',
            'deadline' => 'nullable|date',
            'attachment' => 'nullable|file|max:2048', // max 2MB
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
            'attachment.max' => 'Le fichier ne doit pas dépasser 2 Mo.',
        ];
    }
}