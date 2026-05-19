<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tool extends Model
{
    protected $fillable = [
        'name',
        'description',
        'url',
        'category',
        'category_id',
        'logo_url',
        'is_google_workspace',
        'approval_status',
        'featured',
        'click_count',
        'avg_rating',
        'created_by',
    ];

    protected $casts = [
        'is_google_workspace' => 'boolean',
        'featured' => 'boolean',
        'click_count' => 'integer',
        'avg_rating' => 'decimal:1',
    ];

    // ── Relationships ───────────────────────────────────────────

    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tool_user')->withPivot('created_at');
    }

    public function views(): HasMany
    {
        return $this->hasMany(ToolView::class);
    }

    // ── Scopes ──────────────────────────────────────────────────

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopePopular($query)
    {
        return $query->orderByDesc('click_count');
    }

    public function scopeTopRated($query)
    {
        return $query->orderByDesc('avg_rating');
    }

    // ── Helpers ─────────────────────────────────────────────────

    /**
     * Recalculate and cache the average rating.
     */
    public function recalculateRating(): void
    {
        $this->avg_rating = $this->reviews()->avg('rating') ?? 0;
        $this->saveQuietly();
    }

    /**
     * Increment click counter.
     */
    public function trackClick(?int $userId = null, ?string $ip = null): void
    {
        $this->increment('click_count');
        $this->views()->create([
            'user_id' => $userId,
            'ip_address' => $ip,
        ]);
    }
}
