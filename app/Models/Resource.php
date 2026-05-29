<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'url',
        'file_path',
        'type',
        'area',
        'target_roles',
        'thumbnail_url',
        'author',
        'source',
        'is_published',
        'created_by',
    ];

    protected $casts = [
        'target_roles' => 'array',
        'is_published'  => 'boolean',
    ];

    // ── Area Color Map ───────────────────────────────────────────
    public const AREA_COLORS = [
        'stem'        => 'green',
        'innovation'  => 'orange',
        'ai'          => 'indigo',
        'robotics'    => 'sky',
        'design'      => 'pink',
        'programming' => 'violet',
        'math'        => 'amber',
        'science'     => 'teal',
        'general'     => 'gray',
    ];

    public const AREA_LABELS = [
        'stem'        => 'STEM',
        'innovation'  => 'Innovation',
        'ai'          => 'Artificial Intelligence',
        'robotics'    => 'Robotics',
        'design'      => 'Design',
        'programming' => 'Programming',
        'math'        => 'Mathematics',
        'science'     => 'Science',
        'general'     => 'General',
    ];

    public const TYPE_ICONS = [
        'link'    => '🔗',
        'book'    => '📖',
        'video'   => '🎥',
        'article' => '📰',
        'tool'    => '🛠️',
        'course'  => '🎓',
    ];

    // ── Scopes ───────────────────────────────────────────────────

    /**
     * Only published resources.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Filter by area.
     */
    public function scopeByArea($query, ?string $area)
    {
        if ($area && $area !== 'all') {
            $query->where('area', $area);
        }
        return $query;
    }

    /**
     * Filter by type.
     */
    public function scopeByType($query, ?string $type)
    {
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }
        return $query;
    }

    /**
     * Filter for a given role (student/teacher) or 'all'.
     */
    public function scopeForRole($query, string $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->whereJsonContains('target_roles', 'all')
              ->orWhereJsonContains('target_roles', $role);
        });
    }

    /**
     * Search by title or description.
     */
    public function scopeSearch($query, ?string $term)
    {
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%")
                  ->orWhere('author', 'like', "%{$term}%")
                  ->orWhere('source', 'like', "%{$term}%");
            });
        }
        return $query;
    }

    // ── Relationships ─────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Users who bookmarked this resource.
     */
    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'resource_user')
                    ->withPivot('saved_at')
                    ->withTimestamps();
    }

    // ── Helpers ───────────────────────────────────────────────────

    public function areaColor(): string
    {
        return self::AREA_COLORS[$this->area] ?? 'gray';
    }

    public function areaLabel(): string
    {
        return self::AREA_LABELS[$this->area] ?? ucfirst($this->area);
    }

    public function typeIcon(): string
    {
        return self::TYPE_ICONS[$this->type] ?? '📄';
    }

    public function isExternalLink(): bool
    {
        return !empty($this->url);
    }
}
