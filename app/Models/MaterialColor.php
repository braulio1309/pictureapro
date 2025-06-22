<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use App\Traits\InteractsWithMediaCustom;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaterialColor extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMediaCustom;

    protected $fillable = [
        'material_id',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function getCoverAttribute()
    {
        return $this->getFirstMediaUrlCustom('cover');
    }
}
