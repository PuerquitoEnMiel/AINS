<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'category',
        'difficulty',
        'criteria_type',
        'criteria_config',
        'sort_order',
        // Evidence-based fields
        'requires_evidence',
        'certification_url',
        'evidence_instructions',
        'expires_in_days',
    ];

    protected $casts = [
        'criteria_config'   => 'array',
        'requires_evidence' => 'boolean',
        'expires_in_days'   => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('earned_at', 'score')
            ->withTimestamps();
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    public function evidences()
    {
        return $this->hasMany(BadgeEvidence::class);
    }

    // ── Helpers ─────────────────────────────────────────────────

    /** Whether this badge is permanently valid (no expiry). */
    public function isPermanent(): bool
    {
        return is_null($this->expires_in_days);
    }

    /** Human-readable expiry label. */
    public function expiryLabel(): string
    {
        if ($this->isPermanent()) {
            return 'Permanente';
        }
        $years = round($this->expires_in_days / 365, 1);
        return $years >= 1 ? "{$years} año(s)" : "{$this->expires_in_days} días";
    }
}
