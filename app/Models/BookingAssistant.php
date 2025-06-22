<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAssistant extends Model
{
    protected $fillable = [
        'booking_id',
        'name',
        'birthday'
    ];

    protected $casts = [
        'birthday' => 'date'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
