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

    /** Human-readable validity label (e.g. "3 years", "Permanent"). */
    public function validityLabel(): string
    {
        if (!$this->hasExpiry()) {
            return 'Permanent';
        }

        $days = $this->validity_days;

        if ($days % 365 === 0) {
            $years = $days / 365;
            return $years . ' ' . ($years === 1 ? 'year' : 'years');
        }
        if ($days % 30 === 0) {
            $months = $days / 30;
            return $months . ' ' . ($months === 1 ? 'month' : 'months');
        }

        return $days . ' days';
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

    /**
     * Compute expiry status for a user who earned this badge at $earnedAt.
     *
     * @return array{status: string, days_remaining: int|null, expires_at: \Carbon\Carbon|null, progress: float}
     *   status: 'permanent' | 'active' | 'warning' | 'expired'
     *   warning = less than 30 days remaining
     */
    public function expiryStatusFor(\Carbon\Carbon $earnedAt): array
    {
        if (!$this->hasExpiry()) {
            return [
                'status' => 'permanent',
                'days_remaining' => null,
                'expires_at' => null,
                'progress' => 0,
            ];
        }

        $expiresAt = $earnedAt->copy()->addDays($this->validity_days);
        $now = now();
        $daysRemaining = (int) max(0, $now->diffInDays($expiresAt, false));
        $daysElapsed = (int) $earnedAt->diffInDays($now);
        $progress = min(100, round(($daysElapsed / $this->validity_days) * 100, 1));

        if ($expiresAt->isPast()) {
            $status = 'expired';
        } elseif ($daysRemaining <= 30) {
            $status = 'warning';
        } else {
            $status = 'active';
        }

        return [
            'status' => $status,
            'days_remaining' => $daysRemaining,
            'expires_at' => $expiresAt,
            'progress' => $progress,
        ];
    }
}
