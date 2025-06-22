<?php

namespace App\Models;

use App\Enums\ProductType;
use App\Traits\Multitenantable;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use App\Traits\InteractsWithMediaCustom;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model implements HasMedia
{
    use SoftDeletes;
    use Multitenantable;
    use InteractsWithMediaCustom;

    protected $fillable = [
        'tenant_id',
        'name',
        'is_active',
        'type',
        'description',
        'price',
        'min_photos',
        'max_photos',
        'min_pages',
        'max_pages',
        'page_price',
        'group_by',
        'has_sizes',
    ];

    protected $casts = [
        'type' => ProductType::class,
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'page_price' => 'decimal:2',
        'has_sizes' => 'boolean',
    ];

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'products_materials')->withPivot('material_color_id');
    }

    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }
}
