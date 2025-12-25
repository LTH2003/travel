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
        'title', 'destination', 'description', 'price', 
        'original_price', 'duration', 'image', 'rating', 
        'review_count', 'created_by', 'category', 'max_guests', 
        'highlights', 'includes', 'itinerary', 'departure'
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
     * Relationship: Booking details (orders) for this tour
     */
    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class, 'bookable_id')->where('bookable_type', 'App\\Models\\Tour');
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

    /**
     * Get itinerary attribute - transform objects to strings
     * Handles both old format (array of objects) and new format (array of strings)
     */
    public function getItineraryAttribute($value)
    {
        if (!$value) {
            return [];
        }

        $itinerary = is_string($value) ? json_decode($value, true) : $value;
        
        if (!is_array($itinerary) || empty($itinerary)) {
            return [];
        }

        // Transform itinerary items
        $transformed = array_map(function($item) {
            // If it's an object-like array with 'day' and 'title' keys (old format)
            if (is_array($item) && isset($item['day']) && isset($item['title'])) {
                $day = trim($item['day'] ?? '');
                $title = trim($item['title'] ?? '');
                $combined = trim("$day: $title");
                return !empty($combined) ? $combined : null;
            }
            // Otherwise treat as string
            $str = trim((string)$item);
            return !empty($str) ? $str : null;
        }, $itinerary);

        // Remove null/empty values and reindex
        return array_values(array_filter($transformed, fn($item) => $item !== null));
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
}
