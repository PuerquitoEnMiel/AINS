<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'role',
        'department',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ── Role Helpers ────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    // ── Relationships ───────────────────────────────────────────

    /**
     * Tools this user has favorited.
     */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Tool::class, 'tool_user')->withPivot('created_at');
    }

    /**
     * Reviews written by this user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Chat conversations owned by this user.
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(ChatConversation::class);
    }

    /**
     * Tool requests submitted by this user.
     */
    public function toolRequests(): HasMany
    {
        return $this->hasMany(ToolRequest::class, 'requester_email', 'email');
    }

    /**
     * Tools created/added by this user (admin/teacher).
     */
    public function createdTools(): HasMany
    {
        return $this->hasMany(Tool::class, 'created_by');
    }
}
