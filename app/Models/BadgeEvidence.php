<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadgeEvidence extends Model
{
    protected $fillable = [
        'user_id',
        'badge_id',
        'file_path',
        'file_name',
        'file_type',
        'certificate_url',
        'notes',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'expires_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'expires_at'  => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ── Helpers ─────────────────────────────────────────────────

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'  => 'Pendiente',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            default    => 'Desconocido',
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'pending'  => '#F59E0B',
            'approved' => '#10B981',
            'rejected' => '#EF4444',
            default    => '#6B7280',
        };
    }
}
