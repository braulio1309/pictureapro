<?php

namespace App\Models;

use App\Traits\InteractsWithMediaCustom;
use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;


class Gallery extends Model implements HasMedia
{
    use Multitenantable;
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMediaCustom;

    
    protected $fillable = [
        'internal_title',
        'client_title',
        'internal_description',
        'client_description',
        'photos_to_select',
        'max_photos_to_select',
        'expiration_days',
        'download_option',
        'download_selected_only',
        'session_price',
        'additional_photo_price',
        'full_gallery_price',
        'watermark_enabled',
        'show_filenames',
        'comments_option',
        'cash_payment_enabled',
        'bank_transfer_enabled',
        'payment_email_subject',
        'payment_email_body',
        'manual_payment_confirmation_subject',
        'manual_payment_confirmation_body',
        'photo_selection_confirmation_subject',
        'photo_selection_confirmation_body',
        'photo_packages',
        'show_complete_session_button',
        'album_20x20_photos',
        'includes_canvas_60x40',
        'photo_packages',
        'is_active'
    ];

    protected $casts = [
        'watermark_enabled' => 'boolean',
        'show_filenames' => 'boolean',
        'download_selected_only' => 'boolean',
        'cash_payment_enabled' => 'boolean',
        'bank_transfer_enabled' => 'boolean',
        'photo_packages' => 'array',
        'session_price' => 'decimal:2',
        'additional_photo_price' => 'decimal:2',
        'full_gallery_price' => 'decimal:2'
    ];

    // Opciones para los campos enum
    public const DOWNLOAD_OPTIONS = [
        'after_payment' => 'Permitir después del pago',
        'never' => 'No permitir descarga',
        'always' => 'Permitir siempre'
    ];

    public const COMMENTS_OPTIONS = [
        'always' => 'Siempre',
        'selected_only' => 'Solo fotografías seleccionadas',
        'unselected_only' => 'Solo fotografías no seleccionadas',
        'never' => 'Nunca'
    ];

    public function photos()
    {
        return $this->hasMany(GalleryPhoto::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }
}
