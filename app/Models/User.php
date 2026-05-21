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

    /**
     * Prompts created by this user.
     */
    public function promptTips(): HasMany
    {
        return $this->hasMany(PromptTip::class);
    }

    /**
     * Votes cast by this user.
     */
    public function promptVotes(): HasMany
    {
        return $this->hasMany(PromptVote::class);
    }

    /**
     * Comments left by this user.
     */
    public function promptComments(): HasMany
    {
        return $this->hasMany(PromptComment::class);
    }

    /**
     * Badges earned by this user.
     */
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class)->withPivot('earned_at', 'score')->withTimestamps();
    }

    /**
     * Quiz attempts by this user.
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // ── Badge Helpers ───────────────────────────────────────────

    public function hasBadge(string $slug): bool
    {
        return $this->badges()->where('slug', $slug)->exists();
    }

    public function badgeCount(): int
    {
        return $this->badges()->count();
    }

    public function latestBadges(int $limit = 5)
    {
        return $this->badges()->latest('earned_at')->limit($limit)->get();
    }
}
