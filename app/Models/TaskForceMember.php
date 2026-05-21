<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskForceMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'email',
        'description',
        'initials',
        'avatar_color',
        'image_url',
        'sort_order',
    ];
}
