<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'sort_order',
    ];
    protected static function booted(): void
    {
        static::saved(function ($category) {
            \Illuminate\Support\Facades\Cache::forget('welcome_categories');
        });

        static::deleted(function ($category) {
            \Illuminate\Support\Facades\Cache::forget('welcome_categories');
        });
    }

    public function tools(): HasMany
    {
        return $this->hasMany(Tool::class);
    }

    /**
     * Only approved tools count.
     */
    public function approvedTools(): HasMany
    {
        return $this->hasMany(Tool::class)->where('approval_status', 'approved');
    }
}
