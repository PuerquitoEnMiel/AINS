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
    ];

    protected $casts = [
        'criteria_config' => 'array',
    ];

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
}
