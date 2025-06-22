<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use Multitenantable;
    use Notifiable;

    protected $fillable = [
        'name',
        'lastname',
        'email',
        'nif_document',
        'phone_number',
        'address',
        'postal_code',
        'country_id',
        'country_name',
        'province_id',
        'province_name',
        'city_id',
        'city_name',
        'notes',
        'tenant_id',
    ];

    protected $appends = ['fullname'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getFullnameAttribute()
    {
        return trim($this->name).' '.trim($this->lastname);
    }
}
