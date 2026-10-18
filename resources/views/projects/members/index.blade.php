<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('projects.show', $project) }}" class="tf-btn tf-btn-secondary">{{ $project->name }}</a>
                <h2 class="mt-1 text-xl font-semibold text-gray-900">Membres du projet</h2>
            </div>
            <a href="{{ route('projects.show', $project) }}" class="tf-btn tf-btn-secondary">Retour au projet</a>
        </div>
    </x-slot>

    <div class="tf-member-layout">
        <section class="tf-member-section" aria-labelledby="invite-heading">
            <div class="tf-member-section-heading">
                <div><p class="tf-eyebrow">{{ $project->name }}</p><h2 id="invite-heading">Inviter un membre</h2></div>
            </div>
            <form method="POST" action="{{ route('projects.invitations.store', $project) }}" class="tf-invite-form">
                @csrf
                <div class="tf-invite-field">
                    <label for="invite-email">Adresse e-mail du compte TaskFlow</label>
                    <input id="invite-email" type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email" placeholder="nom@exemple.fr">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div class="tf-invite-field">
                    <label for="invite-role">Rôle proposé</label>
                    <select id="invite-role" name="role" required>
                        <option value="editor" @selected(old('role') === 'editor')>Editor</option>
                        <option value="viewer" @selected(old('role') === 'viewer')>Viewer</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>
                <button class="tf-btn tf-btn-primary" type="submit">Envoyer l’invitation</button>
            </form>
        </section>

        <section class="tf-member-section" aria-labelledby="members-heading">
            <div class="tf-member-section-heading"><h2 id="members-heading">Membres</h2><span>{{ $project->members->count() + 1 }}</span></div>
            <div class="tf-member-table-scroll">
                <table class="tf-member-table">
                    <thead><tr><th scope="col">Membre</th><th scope="col">Rôle</th><th scope="col">Actions</th></tr></thead>
                    <tbody>
                        <tr>
                            <td><strong>{{ $project->user->name }}</strong><small>{{ $project->user->email }}</small></td>
                            <td><span class="tf-role-badge tf-role-owner">Owner</span></td>
                            <td><span class="tf-muted-label">Propriétaire</span></td>
                        </tr>
                        @forelse ($project->members as $member)
                            <tr>
                                <td><strong>{{ $member->name }}</strong><small>{{ $member->email }}</small></td>
                                <td>
                                    <form method="POST" action="{{ route('projects.members.update', [$project, $member]) }}" class="tf-role-form">
                                        @csrf
                                        @method('PATCH')
                                        <label class="sr-only" for="member-role-{{ $member->id }}">Rôle de {{ $member->name }}</label>
                                        <select id="member-role-{{ $member->id }}" name="role">
                                            <option value="editor" @selected($member->pivot->role === 'editor')>Editor</option>
                                            <option value="viewer" @selected($member->pivot->role === 'viewer')>Viewer</option>
                                        </select>
                                        <button type="submit" class="tf-btn tf-btn-secondary">Enregistrer</button>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('projects.members.destroy', [$project, $member]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="tf-btn tf-btn-danger">Retirer</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="tf-empty-cell">Aucun membre n’a encore rejoint ce projet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="tf-member-section" aria-labelledby="invitations-heading">
            <div class="tf-member-section-heading"><h2 id="invitations-heading">Invitations en attente</h2><span>{{ $invitations->where('status', 'pending')->count() }}</span></div>
            <div class="tf-member-table-scroll">
                <table class="tf-member-table">
                    <thead><tr><th scope="col">Compte invité</th><th scope="col">Rôle</th><th scope="col">Actions</th></tr></thead>
                    <tbody>
                        @forelse ($invitations->where('status', 'pending') as $invitation)
                            <tr>
                                <td><strong>{{ $invitation->invitedUser->name }}</strong><small>{{ $invitation->invitedUser->email }}</small></td>
                                <td><span class="tf-role-badge">{{ ucfirst($invitation->role) }}</span></td>
                                <td>
                                    <form method="POST" action="{{ route('projects.invitations.destroy', [$project, $invitation]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="tf-btn tf-btn-danger">Annuler</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="tf-empty-cell">Aucune invitation en attente.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>