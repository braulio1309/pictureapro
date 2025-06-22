<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarAvailability extends Model
{
    protected $fillable = [
        'calendar_id',
        'date',
        'start_time',
        'end_time',
        'duration',
        'capacity',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    protected $appends = [
        'start_full',
        'end_full',
    ];

    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }

    public function packs()
    {
        return $this->belongsToMany(
            ServicePack::class,
            'calendar_availability_packs',
            'calendar_availability_id',
            'service_pack_id'
        );
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getStartFullAttribute()
    {
        return $this->date->format('d/m/Y') . ' ' . $this->start_time->format('H:i');
    }

    public function getEndFullAttribute()
    {
        return $this->date->format('d/m/Y') . ' ' . $this->end_time->format('H:i');
    }
}
