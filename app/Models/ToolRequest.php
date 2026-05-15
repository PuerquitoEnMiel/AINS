<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolRequest extends Model
{
    protected $fillable = [
        'tool_name',
        'description',
        'url',
        'category',
        'is_google_workspace',
        'requester_name',
        'requester_email',
        'status',
        'admin_notes',
        'tool_id',
    ];

    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }
}
