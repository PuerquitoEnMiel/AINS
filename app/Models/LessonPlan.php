<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'subject',
        'grade_level',
        'objectives',
        'duration',
        'content',
        'selected_tools',
    ];

    protected $casts = [
        'selected_tools' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
