<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromptTip extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'target_role',
        'category',
        'complexity',
        'description',
        'prompt_text',
        'sort_order',
        'user_id',
        'is_community',
        'is_approved',
        'usage_count',
    ];

    protected $casts = [
        'is_community' => 'boolean',
        'is_approved' => 'boolean',
        'usage_count' => 'integer',
        'sort_order' => 'integer',
    ];

    // ── Relationships ───────────────────────────────────────────

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function votes()
    {
        return $this->hasMany(PromptVote::class);
    }

    public function comments()
    {
        return $this->hasMany(PromptComment::class)->latest();
    }

    // ── Helpers ─────────────────────────────────────────────────

    public function voteCount(): int
    {
        $upvotes = $this->votes()->where('type', 'upvote')->count();
        $downvotes = $this->votes()->where('type', 'downvote')->count();
        return $upvotes - $downvotes;
    }

    public function hasVotedBy(?int $userId, string $type): bool
    {
        if (!$userId) return false;
        return $this->votes()->where('user_id', $userId)->where('type', $type)->exists();
    }
}
