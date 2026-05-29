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
     * Badge evidence submissions by this user.
     */
    public function badgeEvidences(): HasMany
    {
        return $this->hasMany(BadgeEvidence::class);
    }

    /**
     * Badge suggestions submitted by this user.
     */
    public function badgeSuggestions(): HasMany
    {
        return $this->hasMany(BadgeSuggestion::class);
    }

    /**
     * Resources bookmarked/saved by this user.
     */
    public function savedResources(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class, 'resource_user')
                    ->withPivot('saved_at')
                    ->withTimestamps();
    }

    // ── Badge Helpers ───────────────────────────────────────────

    /**
     * Check if user has a badge by slug.
     * Uses in-memory collection if badges already eager-loaded (avoids N+1).
     */
    public function hasBadge(string $slug): bool
    {
        if ($this->relationLoaded('badges')) {
            return $this->badges->contains('slug', $slug);
        }

        return $this->badges()->where('slug', $slug)->exists();
    }

    /**
     * Count earned badges.
     * Uses in-memory collection if badges already eager-loaded.
     */
    public function badgeCount(): int
    {
        if ($this->relationLoaded('badges')) {
            return $this->badges->count();
        }

        return $this->badges()->count();
    }

    public function latestBadges(int $limit = 5)
    {
        return $this->badges()->latest('earned_at')->limit($limit)->get();
    }
}
