<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadgeSuggestion extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'certification_url',
        'status',
        'admin_notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
