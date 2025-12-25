<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content',
        'author','category','tags','image',
        'published_at','read_time','views','likes',
    ];

    protected $casts = [
        'author' => 'array',
        'tags' => 'array',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function comments()
    {
        return $this->hasMany(BlogComment::class);
    }
}
