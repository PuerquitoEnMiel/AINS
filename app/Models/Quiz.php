<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'badge_id',
        'title',
        'description',
        'questions',
        'passing_score',
    ];

    protected $casts = [
        'questions' => 'array',
    ];

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
