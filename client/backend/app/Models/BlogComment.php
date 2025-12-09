<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'user_id',
        'content',
        'parent_id',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship với Blog
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    // Relationship với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship với parent comment (for nested comments)
    public function parent()
    {
        return $this->belongsTo(BlogComment::class, 'parent_id');
    }

    // Relationship với child comments (replies)
    public function replies()
    {
        return $this->hasMany(BlogComment::class, 'parent_id');
    }
}
