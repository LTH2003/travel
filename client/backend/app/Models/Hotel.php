<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'name', 'location', 'description', 'rating',
        'price', 'original_price', 'image', 'images', 'amenities',
        'reviews', 'check_in', 'check_out', 'cancellation', 'children',
        'address', 'city', 'created_by', 'rooms_count',
    ];

    protected $casts = [
        'images' => 'array',
        'amenities' => 'array',
    ];

    /**
     * Relationship: User who created this hotel
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
}