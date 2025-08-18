<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Gallery;
use App\Models\GalleryPhoto;
use App\Models\Product;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class GalleryForm extends Form
{
    use WithFileUploads;

    public ?Gallery $gallery = null;
    
    // Campos básicos
    public $internal_title = '';
    public $client_title = '';
    public $internal_description = '';
    public $client_description = '';
    
    // Opciones
    public $photos_to_select = 2;
    public $max_photos_to_select = 0;
    public $expiration_days = 7;
    public $download_option = 'after_payment';
    public $download_selected_only = true;
    public $watermark_enabled = false;
    public $show_filenames = false;
    public $comments_option = 'never';
    
    // Precios
    public $session_price = null;
    public $additional_photo_price = 5;
    public $full_gallery_price = 20;
    
    // Pago
    public $cash_payment_enabled = false;
    public $bank_transfer_enabled = false;
    
    // Emails
    public $payment_email_subject = '';
    public $payment_email_body = '';
    public $manual_payment_confirmation_subject = '';
    public $manual_payment_confirmation_body = '';
    public $photo_selection_confirmation_subject = '';
    public $photo_selection_confirmation_body = '';
    
    // Estado
    public $is_active = true;
    
    // Productos
    public $show_complete_session_button = false;
    public $album_20x20_photos = null;
    public $includes_canvas_60x40 = false;
    
    // Archivos
    public $cover;
    public $photos = [];
    public $photo_packages = [];
    
    // Productos seleccionados
    public $includedProducts = [];

    public function setGallery(?Gallery $gallery = null)
    {
        $this->gallery = $gallery;
        
        if ($gallery) {
            $this->fill($gallery->only([
                'internal_title', 'client_title', 'internal_description', 'client_description',
                'photos_to_select', 'max_photos_to_select', 'expiration_days', 'download_option',
                'download_selected_only', 'session_price', 'additional_photo_price', 'full_gallery_price',
                'watermark_enabled', 'show_filenames', 'comments_option', 'cash_payment_enabled',
                'bank_transfer_enabled', 'payment_email_subject', 'payment_email_body',
                'manual_payment_confirmation_subject', 'manual_payment_confirmation_body',
                'photo_selection_confirmation_subject', 'photo_selection_confirmation_body',
                'is_active', 'show_complete_session_button', 'album_20x20_photos', 'includes_canvas_60x40'
            ]));
            
            $this->photo_packages = $gallery->photo_packages ?? [];
            $this->includedProducts = $gallery->products->pluck('id')->toArray();
        }
    }

    public function rules()
    {
        return [
            'internal_title' => 'required|string|max:255',
            'client_title' => 'required|string|max:255',
            'photos_to_select' => 'required|integer|min:0',
            'max_photos_to_select' => 'required|integer|min:0',
            'expiration_days' => 'required|integer|min:1',
            'session_price' => 'nullable|numeric|min:0',
            'additional_photo_price' => 'required|numeric|min:0',
            'full_gallery_price' => 'required|numeric|min:0',
            'photos.*' => 'image|max:10240',
            'cover' => 'nullable|image|max:10240',
            'show_complete_session_button' => 'boolean',
            'album_20x20_photos' => 'nullable|integer|min:0',
            'includes_canvas_60x40' => 'boolean',
            'includedProducts' => 'array',
            'includedProducts.*' => 'exists:products,id',
        ];
    }

    public function save()
    {
        $data = $this->all();
        $data['photo_packages'] = $this->photo_packages;
        dd($data['is_active']);
        
        if ($this->gallery) {
            
            $this->gallery->update($data);
            $this->gallery->products()->sync($this->includedProducts);
        } else {
            $this->gallery = Gallery::create($data);
            $this->gallery->products()->attach($this->includedProducts);
        }

        // Procesar portada
        if ($this->cover) {
            $this->gallery->photos()->where('is_cover', true)->delete();
            
            $path = $this->cover->store('public/galleries/' . $this->gallery->id);
            
            GalleryPhoto::create([
                'gallery_id' => $this->gallery->id,
                'filename' => $this->cover->getClientOriginalName(),
                'path' => str_replace('public/', '', $path),
                'has_watermark' => $this->watermark_enabled,
                'is_cover' => true,
            ]);
        }

        // Procesar fotos adicionales
        if (!empty($this->photos)) {
            foreach ($this->photos as $photo) {
                $path = $photo->store('public/galleries/' . $this->gallery->id);
                
                GalleryPhoto::create([
                    'gallery_id' => $this->gallery->id,
                    'filename' => $photo->getClientOriginalName(),
                    'path' => str_replace('public/', '', $path),
                    'has_watermark' => $this->watermark_enabled,
                    'is_cover' => false,
                ]);
            }
        }
        $this->success('Galeria creada correctamente', css: 'bg-primary text-white');

        return true;
    }
}