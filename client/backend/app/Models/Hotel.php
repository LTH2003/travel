<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'name', 'location', 'description', 'rating',
        'price', 'original_price', 'image', 'images', 'amenities',
        'reviews', 'check_in', 'check_out', 'cancellation', 'children',
    ];

    protected $casts = [
        'images' => 'array',
        'amenities' => 'array',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
}