<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">TaskFlow</p>
            <h2 class="mt-1 text-2xl font-semibold text-gray-900">Notifications</h2>
        </div>
    </x-slot>

    <section class="py-8 sm:py-10">
        <div class="mx-auto max-w-4xl space-y-3 px-4 sm:px-6 lg:px-8">
            @forelse ($notifications as $notification)
                @php
                    $data = $notification->data;
                    $invitation = $invitations->get($data['invitation_id'] ?? null);
                    $isPendingInvitation = $invitation?->status === 'pending';
                @endphp

                <article class="border-b border-gray-200 py-5 first:border-t {{ $notification->read_at ? 'bg-white' : 'bg-emerald-50/60' }}">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            @if (isset($data['invitation_id']))
                                <h3 class="font-semibold text-gray-900">Invitation au projet {{ $data['project_name'] ?? 'TaskFlow' }}</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ $data['inviter_name'] ?? 'Un membre' }} vous propose le rôle
                                    <span class="font-medium text-gray-800">{{ ucfirst($data['role'] ?? 'viewer') }}</span>.
                                </p>
                                @if ($isPendingInvitation)
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        <form method="POST" action="{{ route('project-invitations.accept', $invitation) }}">
                                            @csrf
                                            <button type="submit" class="tf-btn tf-btn-primary">Accepter</button>
                                        </form>
                                        <form method="POST" action="{{ route('project-invitations.decline', $invitation) }}">
                                            @csrf
                                            <button type="submit" class="tf-btn tf-btn-secondary">Refuser</button>
                                        </form>
                                    </div>
                                @else
                                    <p class="mt-3 text-sm font-medium text-gray-500">
                                        {{ match ($invitation?->status) {
                                            'accepted' => 'Invitation acceptée',
                                            'declined' => 'Invitation refusée',
                                            'cancelled' => 'Invitation annulée',
                                            default => 'Invitation indisponible',
                                        } }}
                                    </p>
                                @endif
                            @else
                                <p class="font-medium text-gray-900">{{ $data['message'] ?? 'Notification TaskFlow' }}</p>
                            @endif
                            <p class="mt-2 text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>

                        @if (! $notification->read_at)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="tf-btn tf-btn-secondary">Marquer comme lue</button>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="border-y border-gray-200 py-12 text-center">
                    <h3 class="text-lg font-semibold text-gray-900">Aucune notification</h3>
                    <p class="mt-2 text-sm text-gray-600">Les invitations de projets apparaîtront ici.</p>
                </div>
            @endforelse

            {{ $notifications->links() }}
        </div>
    </section>
</x-app-layout>