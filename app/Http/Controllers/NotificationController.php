<?php

namespace App\Http\Controllers;

use App\Models\ProjectInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);
        $invitationIds = $notifications->getCollection()
            ->map(fn ($notification) => $notification->data['invitation_id'] ?? null)
            ->filter()
            ->unique();
        $invitations = ProjectInvitation::query()
            ->with(['project', 'inviter'])
            ->whereKey($invitationIds)
            ->get()
            ->keyBy('id');

        return view('notifications.index', compact('notifications', 'invitations'));
    }

    public function markAsRead(Request $request, string $notification): RedirectResponse
    {
        $request->user()->notifications()->findOrFail($notification)->markAsRead();

        return redirect()->route('notifications.index');
    }
}
