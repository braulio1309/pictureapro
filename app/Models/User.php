<?php

namespace App\Models;

use App\Traits\InteractsWithMediaCustom;
use App\Traits\Multitenantable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use LucasDotVin\Soulbscription\Models\Concerns\HasSubscriptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, HasMedia
{
    use HasFactory;
    use HasRoles;
    use HasSubscriptions;
    use InteractsWithMediaCustom;
    use Impersonate;
    use SoftDeletes;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'email',
        'email_verified_at',
        'password',
        'is_enabled',
        'company_name',
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
        'timezone',
        'notes',
        'tenant_id',
        'transfer_details',
        'stripe_pub',
        'stripe_priv',
        'stripe_wh_id',
        'stripe_wh_secret',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'fullname',
        'has_stripe',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_enabled' => 'boolean',
        ];
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function canImpersonate(): bool
    {
        return $this->hasRole('admin');
    }
    
    public function canBeImpersonated(): bool
    {
        return !$this->hasRole('admin');
    }

    public function getFullnameAttribute()
    {
        return trim($this->name).' '.trim($this->lastname);
    }

    public function getHasStripeAttribute()
    {
        return !empty($this->stripe_pub) && !empty($this->stripe_priv) && !empty($this->stripe_wh_id) && !empty($this->stripe_wh_secret);
    }
}
