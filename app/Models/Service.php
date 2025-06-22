<?php

namespace App\Models;

use App\Traits\InteractsWithMediaCustom;
use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class Service extends Model implements HasMedia
{
    use Multitenantable;
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMediaCustom;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'with_reservation',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'with_reservation' => 'boolean',
    ];

    public function packs()
    {
        return $this->hasMany(ServicePack::class);
    }

    public function getCoverAttribute()
    {
        return $this->getFirstMediaUrlCustom('cover');
    }
}
