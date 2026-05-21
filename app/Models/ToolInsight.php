<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolInsight extends Model
{
    protected $fillable = [
        'tool_id',
        'summary',
        'pros',
        'cons',
        'best_for_grades',
        'best_use_cases',
        'generated_at',
        'review_count_at_generation',
    ];

    protected $casts = [
        'pros' => 'array',
        'cons' => 'array',
        'best_for_grades' => 'array',
        'best_use_cases' => 'array',
        'generated_at' => 'datetime',
    ];

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }

    /**
     * Check if insight is stale (new reviews added since generation).
     */
    public function isStale(): bool
    {
        return $this->tool->reviews()->count() > $this->review_count_at_generation;
    }
}
