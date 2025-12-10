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
        'title', 'destination', 'description', 'price', 'original_price', 'duration', 'image', 'rating', 'review_count', 'created_by', 'category', 'max_guests', 'highlights', 'includes', 'itinerary', 'departure'
    ];

    /**
     * Relationship: User who created this tour
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: Reviews for this tour
     */
    public function reviews()
    {
        return $this->hasMany(TourReview::class);
    }

    /**
     * Get approved reviews count
     */
    public function approvedReviewsCount()
    {
        return $this->reviews()->where('is_approved', true)->count();
    }

    /**
     * Get average rating from approved reviews
     */
    public function getAverageRating()
    {
        $avg = $this->reviews()
            ->where('is_approved', true)
            ->avg('rating');
        return $avg ? round($avg, 1) : 0;
    }

    /**
     * Update tour rating based on approved reviews
     */
    public function updateRating()
    {
        $avgRating = $this->getAverageRating();
        $reviewCount = $this->approvedReviewsCount();
        $this->update([
            'rating' => $avgRating,
            'review_count' => $reviewCount,
        ]);
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
}
