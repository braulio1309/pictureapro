<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'calendar_id',
        'calendar_availability_id',
        'client_id',
        'service_pack_id',
        'status',
        'code',
        'name',
        'notes',
        'allow_share',
        'source_id',
        'manual',
    ];

    protected $casts = [
        'status' => BookingStatus::class,
        'allow_share' => 'boolean',
        'manual' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($booking) {
            $booking->answers()->delete();
            $booking->assistants()->delete();
            $booking->payment()->delete();
        });
    }

    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }

    public function availability()
    {
        return $this->belongsTo(CalendarAvailability::class, 'calendar_availability_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function pack()
    {
        return $this->belongsTo(ServicePack::class, 'service_pack_id');
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function answers()
    {
        return $this->hasMany(BookingAnswer::class);
    }

    public function assistants()
    {
        return $this->hasMany(BookingAssistant::class);
    }

    public function payment()
    {
        return $this->hasOne(BookingPayment::class);
    }
}
