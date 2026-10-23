<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalInfo extends Model
{
    use HasFactory;

    protected $table = 'personal_infos';

    protected $fillable = [
        'name',
        'title',
        'bio',
        'email',
        'phone',
        'location',
        'avatar',
        'resume_url',
        'github_url',
        'linkedin_url',
        'social_links',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
        ];
    }
}
