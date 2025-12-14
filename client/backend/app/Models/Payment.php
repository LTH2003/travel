<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'status',
        'amount',
        'payment_method',
        'request_id',
        'response_data',
        'error_message',
        'paid_at',
    ];

    protected $casts = [
        'response_data' => 'json',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Mock mode: Override status to 'success' for pending payments
     */
    protected $appends = ['display_status'];

    public function getDisplayStatusAttribute()
    {
        // Nếu APP_PAYMENT_MOCK=true, hiển thị pending là success
        if (env('APP_PAYMENT_MOCK', false) && $this->status === 'pending') {
            return 'success';
        }
        return $this->status;
    }

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
