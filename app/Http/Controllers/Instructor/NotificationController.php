<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Auth::user()->customNotifications()
            ->with(['sender'])
            ->where(function ($q) {
                $q->whereNull('audience')->orWhere('audience', 'instructor');
            })
            ->latest();

        if ($request->filled('status') && $request->status === 'unread') {
            $query->where('is_read', false);
        }

        $notifications = $query->paginate(25)->withQueryString();

        return view('instructor.notifications.index', compact('notifications'));
    }

    public function go(Notification $notification): RedirectResponse
    {
        $this->authorizeOwned($notification);
        if (! $notification->is_read) {
            $notification->update(['is_read' => true, 'read_at' => now()]);
        }

        $url = $notification->action_url ?: route('instructor.notifications.index');

        return redirect()->to($url);
    }

    public function markAsRead(Notification $notification): RedirectResponse
    {
        $this->authorizeOwned($notification);
        $notification->update(['is_read' => true, 'read_at' => now()]);

        return back()->with('success', 'تم تعليم الإشعار كمقروء.');
    }

    public function markAllAsRead(): RedirectResponse
    {
        Auth::user()->customNotifications()
            ->where(function ($q) {
                $q->whereNull('audience')->orWhere('audience', 'instructor');
            })
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return back()->with('success', 'تم تعليم كل الإشعارات كمقروءة.');
    }

    public function unreadCount(): JsonResponse
    {
        $count = Auth::user()->customNotifications()
            ->where(function ($q) {
                $q->whereNull('audience')->orWhere('audience', 'instructor');
            })
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    private function authorizeOwned(Notification $notification): void
    {
        abort_unless((int) $notification->user_id === (int) Auth::id(), 403);
    }
}
