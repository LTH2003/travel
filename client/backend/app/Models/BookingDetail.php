<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'bookable_type',
        'bookable_id',
        'quantity',
        'price',
        'booking_info',
    ];

    protected $casts = [
        'booking_info' => 'json',
        'price' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function bookable()
    {
        return $this->morphTo();
    }
}
