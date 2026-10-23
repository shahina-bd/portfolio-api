<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'technologies',
        'github_url',
        'live_url',
        'featured',
    ];

    protected function casts(): array
    {
        return [
            'technologies' => 'array',
            'featured' => 'boolean',
        ];
    }
}
