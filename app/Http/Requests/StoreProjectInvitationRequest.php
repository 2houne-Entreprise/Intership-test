<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProjectInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $project = Project::query()->find($this->route('project'));

        return $project !== null && Gate::allows('manageMembers', $project);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255', Rule::exists('users', 'email')],
            'role' => ['required', Rule::in(['editor', 'viewer'])],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->has('email')) {
                return;
            }

            $project = Project::query()->find($this->route('project'));
            $invitee = User::query()->where('email', $this->input('email'))->first();

            if ($project === null || $invitee === null) {
                return;
            }

            if ($project->isOwnedBy($invitee) || $project->members()->whereKey($invitee->id)->exists()) {
                $validator->errors()->add('email', 'Cet utilisateur est déjà membre du projet.');

                return;
            }

            if ($project->invitations()->where('invited_user_id', $invitee->id)->where('status', 'pending')->exists()) {
                $validator->errors()->add('email', 'Une invitation est déjà en attente pour cet utilisateur.');
            }
        }];
    }
}
