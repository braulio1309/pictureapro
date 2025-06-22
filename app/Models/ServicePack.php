<?php

namespace App\Models;

use App\Traits\InteractsWithMediaCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class ServicePack extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMediaCustom;

    protected $fillable = [
        'service_id',
        'name',
        'is_active',
        'description',
        'duration',
        'price',
        'reservation_price',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
