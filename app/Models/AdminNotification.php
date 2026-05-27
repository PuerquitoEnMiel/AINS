<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'data',
        'read_at',
    ];

    protected static function booted()
    {
        static::creating(function ($notification) {
            $template = \App\Models\NotificationTemplate::where('type', $notification->type)->first();
            if ($template) {
                $user = \App\Models\User::find($notification->user_id);
                $userName = $user ? $user->name : 'System';
                
                $badgeName = $notification->data['badge_name'] ?? '';
                $suggestionName = $notification->data['suggestion_name'] ?? '';
                
                $search = ['{user}', '{badge}', '{suggestion}'];
                $replace = [$userName, $badgeName, $suggestionName];
                
                $notification->title = str_replace($search, $replace, $template->subject);
                $notification->message = str_replace($search, $replace, $template->template);
            }
        });

        static::created(function ($notification) {
            try {
                $admins = \App\Models\User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    \Illuminate\Support\Facades\Mail::to($admin->email)
                        ->send(new \App\Mail\AdminAlertMail($notification));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send admin alert email: ' . $e->getMessage());
            }
        });
    }

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
