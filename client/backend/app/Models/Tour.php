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
        'name', 'slug', 'destination', 'description', 'price', 'duration', 'image', 'rating'
    ];

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
}
