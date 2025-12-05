<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_code',
        'total_amount',
        'status',
        'payment_method',
        'items',
        'notes',
        'completed_at',
        'email_sent_at',
    ];

    protected $casts = [
        'items' => 'json',
        'total_amount' => 'decimal:2',
        'completed_at' => 'datetime',
        'email_sent_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function lastPayment()
    {
        return $this->hasOne(Payment::class)->latest();
    }

    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class);
    }

    public function purchaseHistory()
    {
        return $this->hasMany(PurchaseHistory::class);
    }
}
