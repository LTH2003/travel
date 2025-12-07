<?php
// app/Models/Tour.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;

    protected $casts = [
    'highlights' => 'array',
    'includes' => 'array',
    'itinerary' => 'array',
    'departure' => 'array',
];

    protected $fillable = [
        'title', 'destination', 'description', 'price', 'original_price', 'duration', 'image', 'rating', 'created_by', 'category', 'max_guests'
    ];

    /**
     * Relationship: User who created this tour
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
}
