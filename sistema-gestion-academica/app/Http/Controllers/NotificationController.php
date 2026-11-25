<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->appNotifications()
            ->latest()
            ->paginate(15);

        $unreadCount = $request->user()->unreadNotificationsCount();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markRead(Request $request, AppNotification $notification): RedirectResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(403);
        }

        $notification->markAsRead();

        return back()->with('success', 'Notificación marcada como leída.');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()
            ->appNotifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Todas las notificaciones fueron marcadas como leídas.');
    }
}
