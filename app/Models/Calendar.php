<?php

namespace App\Models;

use App\Traits\InteractsWithMediaCustom;
use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class Calendar extends Model implements HasMedia
{
    use SoftDeletes;
    use Multitenantable;
    use InteractsWithMediaCustom;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'is_active',
        'show_busy',
        'require_address',
        'require_nif_document',
        'require_assistants',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_busy' => 'boolean',
        'require_address' => 'boolean',
        'require_nif_document' => 'boolean',
        'require_assistants' => 'boolean',
    ];

    public function availabilities()
    {
        return $this->hasMany(CalendarAvailability::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'calendar_services');
    }

    public function questions()
    {
        return $this->hasMany(CalendarQuestion::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getCoverAttribute()
    {
        return $this->getFirstMediaUrlCustom('cover');
    }


}
