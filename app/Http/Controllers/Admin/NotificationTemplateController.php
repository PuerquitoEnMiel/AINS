<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;

class NotificationTemplateController extends Controller
{
    public function index()
    {
        $templates = NotificationTemplate::all();
        return view('admin.notification_templates.index', compact('templates'));
    }

    public function edit(NotificationTemplate $template)
    {
        return view('admin.notification_templates.edit', compact('template'));
    }

    public function update(Request $request, NotificationTemplate $template)
    {
        $request->validate([
            'subject'  => 'required|string|max:255',
            'template' => 'required|string|max:2000',
        ]);

        $template->update([
            'subject'  => $request->subject,
            'template' => $request->template,
        ]);

        return redirect()->route('admin.notification-templates.index')
            ->with('success', 'Plantilla de notificación actualizada con éxito.');
    }
}
