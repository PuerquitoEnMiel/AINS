<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AdminNotification::with('user')
            ->orderBy('read_at', 'asc')
            ->latest()
            ->paginate(15);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(AdminNotification $notification)
    {
        $notification->update(['read_at' => now()]);

        return back()->with('success', 'Notificación marcada como leída.');
    }

    public function readAll()
    {
        AdminNotification::whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    }
}
