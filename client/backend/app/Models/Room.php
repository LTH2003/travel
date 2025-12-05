<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'hotel_id', 'name', 'size', 'capacity', 'beds', 'price', 'original_price',
        'description', 'images', 'amenities', 'available'
    ];

    protected $casts = [
        'images' => 'array',
        'amenities' => 'array',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
