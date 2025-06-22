<?php

namespace App\Models;

use App\Traits\InteractsWithMediaCustom;
use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class Material extends Model implements HasMedia
{
    use Multitenantable;
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMediaCustom;

    protected $fillable = [
        'tenant_id',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function colors()
    {
        return $this->hasMany(MaterialColor::class);
    }

    public function getCoverAttribute()
    {
        return $this->getFirstMediaUrlCustom('cover');
    }
}
