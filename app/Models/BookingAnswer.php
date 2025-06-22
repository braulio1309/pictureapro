<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAnswer extends Model
{
    protected $fillable = [
        'booking_id',
        'calendar_question_id',
        'answer'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function question()
    {
        return $this->belongsTo(CalendarQuestion::class);
    }
}
