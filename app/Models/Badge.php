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
        'image_path',
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
        'validity_days',
        'is_mandatory',
    ];

    protected $casts = [
        'criteria_config'   => 'array',
        'requires_evidence' => 'boolean',
        'is_mandatory'      => 'boolean',
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

    /** Whether this badge expires after a set period. */
    public function hasExpiry(): bool
    {
        return !is_null($this->validity_days) && $this->validity_days > 0;
    }

    /** Human-readable validity label (e.g. "3 años", "Permanente"). */
    public function validityLabel(): string
    {
        if (!$this->hasExpiry()) {
            return 'Permanente';
        }

        $days = $this->validity_days;

        if ($days % 365 === 0) {
            $years = $days / 365;
            return $years . ' ' . ($years === 1 ? 'año' : 'años');
        }
        if ($days % 30 === 0) {
            $months = $days / 30;
            return $months . ' ' . ($months === 1 ? 'mes' : 'meses');
        }

        return $days . ' días';
    }

    /** Calculate individual expiry date from a given approval date. */
    public function calculateExpiresAt(\Carbon\Carbon $approvedAt = null): ?\Carbon\Carbon
    {
        if (!$this->hasExpiry()) {
            return null;
        }
        $from = $approvedAt ?? now();
        return $from->copy()->addDays($this->validity_days);
    }
}
