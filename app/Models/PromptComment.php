<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromptComment extends Model
{
    protected $fillable = [
        'user_id',
        'prompt_tip_id',
        'body',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function promptTip(): BelongsTo
    {
        return $this->belongsTo(PromptTip::class);
    }
}
