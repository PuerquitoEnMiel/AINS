<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    protected $fillable = [
        'name',
        'description',
        'url',
        'category',
        'logo_url',
        'is_google_workspace',
        'approval_status',
    ];
}
