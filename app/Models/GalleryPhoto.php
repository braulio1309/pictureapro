<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\InteractsWithMediaCustom;
use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class GalleryPhoto extends Model implements HasMedia
{
    use Multitenantable;
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMediaCustom;

    protected $fillable = [
        'gallery_id',
        'path',
    ];


    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }

     public function getCoverAttribute()
    {
        return $this->getFirstMediaUrlCustom('path');
    }
}
