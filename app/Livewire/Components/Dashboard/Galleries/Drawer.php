<?php

namespace App\Livewire\Components\Dashboard\Galleries;

use App\Models\Gallery;
use App\Models\GalleryPhoto;
use Mary\Traits\Toast;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Storage;

class Drawer extends Component
{
    use WithFileUploads;
    use Toast;

    public bool $open = false;
    public string $action = 'create';
    public string $tab_selected = 'general';
    public $coverThumbnail;
    
    public $cover;
    public string $cover_key;
    public array $photos = [];
    public string $photos_key;
    
    public array $form = [
        'internal_title' => '',
        'client_title' => '',
        'internal_description' => '',
        'client_description' => '',
        'photos_to_select' => 2,
        'max_photos_to_select' => 0,
        'expiration_days' => 7,
        'download_option' => 'after_payment',
        'download_selected_only' => true,
        'session_price' => null,
        'additional_photo_price' => 5,
        'full_gallery_price' => 20,
        'watermark_enabled' => false,
        'show_filenames' => false,
        'comments_option' => 'never',
        'cash_payment_enabled' => false,
        'bank_transfer_enabled' => false,
        'payment_email_subject' => '',
        'payment_email_body' => '',
        'manual_payment_confirmation_subject' => '',
        'manual_payment_confirmation_body' => '',
        'photo_selection_confirmation_subject' => '',
        'photo_selection_confirmation_body' => '',
        'is_active' => true,
        'show_complete_session_button' => false,
        'album_20x20_photos' => null,
        'includes_canvas_60x40' => false,
    ];
    
    public array $photo_packages = [];
    public $new_package_quantity;
    public $new_package_price;
    
    public array $includedProducts = [];
    public array $availableProducts = [];
    
    public ?Gallery $gallery = null;

    protected $rules = [
        'form.internal_title' => 'required|string|max:255',
        'form.client_title' => 'required|string|max:255',
        'form.photos_to_select' => 'required|integer|min:0',
        'form.max_photos_to_select' => 'required|integer|min:0',
        'form.expiration_days' => 'required|integer|min:1',
        'form.session_price' => 'nullable|numeric|min:0',
        'form.additional_photo_price' => 'required|numeric|min:0',
        'form.full_gallery_price' => 'required|numeric|min:0',
        'photos.*' => 'image|max:10240',
        'cover' => 'nullable|image|max:10240',
        'form.show_complete_session_button' => 'boolean',
        'form.album_20x20_photos' => 'nullable|integer|min:0',
        'form.includes_canvas_60x40' => 'boolean',
        'includedProducts' => 'array',
        'includedProducts.*' => 'exists:products,id',
    ];

    #[On('galleries:open-drawer')]
    public function open(string $action = 'create', ?int $id = null): void
    {
        $this->action = $action;
        $this->resetDrawer();
        
        if ($this->action === 'edit' && $id) {
            $this->gallery = Gallery::with(['photos', 'products'])->findOrFail($id);
            $this->loadGalleryData();
        }
        
        $this->loadProducts();
        $this->open = true;
    }

    public function loadGalleryData()
    {
        foreach ($this->form as $key => $value) {
            if (array_key_exists($key, $this->gallery->getAttributes())) {
                $this->form[$key] = $this->gallery->$key;
            }
        }
        
        $this->photo_packages = $this->gallery->photo_packages ?? [];
        
        $coverPhoto = $this->gallery->photos()->where('is_cover', true)->first();
        if ($coverPhoto) {
            $this->coverThumbnail = Storage::url($coverPhoto->path);
        }
    }

    protected function loadProducts()
    {
        $this->availableProducts = Product::query()
            ->where('is_active', true)
            ->where('tenant_id', Auth::user()->id)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'type' => $product->type
                ];
            })
            ->toArray();
            
        if ($this->action === 'edit' && $this->gallery) {
            $this->includedProducts = $this->gallery->products->pluck('id')->toArray();
        }
    }

    public function toggleProduct($productId)
    {
        if (in_array($productId, $this->includedProducts)) {
            $this->includedProducts = array_diff($this->includedProducts, [$productId]);
        } else {
            $this->includedProducts[] = $productId;
        }
    }

    public function coverThumbnail()
    {
        if ($this->cover) {
            return $this->cover->temporaryUrl();
        }
        
        return $this->coverThumbnail ?? asset('images/placeholder.webp');
    }

    public function photosThumbnails(): array
    {
        if (!$this->gallery) return [];
        
        $photosFromDB = $this->gallery->photos()
            ->where('is_cover', false)
            ->get()
            ->map(function ($photo) {
                return [
                    'id' => (string) $photo->id,
                    'url' => Storage::url($photo->path),
                    'filename' => $photo->filename,
                    'from' => 'gallery'
                ];
            })->toArray();
            
        $photosFromInput = array_map(function ($photo) {
            return [
                'id' => $photo->getFilename(),
                'url' => $photo->temporaryUrl(),
                'filename' => $photo->getClientOriginalName(),
                'from' => 'input'
            ];
        }, $this->photos);
        
        return array_merge($photosFromDB, $photosFromInput);
    }

    public function addPhotoPackage()
    {
        if ($this->new_package_quantity && $this->new_package_price) {
            $this->photo_packages[] = [
                'quantity' => $this->new_package_quantity,
                'price' => $this->new_package_price
            ];
            $this->new_package_quantity = null;
            $this->new_package_price = null;
        }
    }

    public function removePhotoPackage($index)
    {
        unset($this->photo_packages[$index]);
        $this->photo_packages = array_values($this->photo_packages);
    }

    public function submit()
    {
        $this->validate();

        $data = $this->form;
        $data['photo_packages'] = $this->photo_packages;
        
        if ($this->action === 'edit') {
            $this->gallery->update($data);
            $this->gallery->products()->sync($this->includedProducts);
            $message = 'Galería actualizada correctamente';
        } else {
            $this->gallery = Gallery::create($data);
            $this->gallery->products()->attach($this->includedProducts);
            $this->action = 'edit';
            $message = 'Galería creada correctamente';
        }

        if ($this->cover) {
            $this->gallery->photos()->where('is_cover', true)->delete();
            
            $path = $this->cover->store('public/galleries/' . $this->gallery->id);
            
            GalleryPhoto::create([
                'gallery_id' => $this->gallery->id,
                'filename' => $this->cover->getClientOriginalName(),
                'path' => str_replace('public/', '', $path),
                'has_watermark' => $this->form['watermark_enabled'],
                'is_cover' => true,
            ]);
            
            $this->cover = null;
            $this->cover_key = rand();
        }

        if (!empty($this->photos)) {
            foreach ($this->photos as $photo) {
                $path = $photo->store('public/galleries/' . $this->gallery->id);
                
                GalleryPhoto::create([
                    'gallery_id' => $this->gallery->id,
                    'filename' => $photo->getClientOriginalName(),
                    'path' => str_replace('public/', '', $path),
                    'has_watermark' => $this->form['watermark_enabled'],
                    'is_cover' => false,
                ]);
            }
            
            $this->photos = [];
            $this->photos_key = rand();
        }

        $this->dispatch('notify', type: 'success', message: $message);
        $this->dispatch('galleries:updated');
        $this->success('Galeria creada con exito');

        
    }

    public function deletePhoto(string $id)
    {
        $photo = collect($this->photosThumbnails)->firstWhere('id', $id);
        
        if ($photo['from'] === 'input') {
            $this->photos = array_filter($this->photos, fn($p) => $p->getFilename() !== $id);
        }
        
        if ($photo['from'] === 'gallery') {
            $photo = GalleryPhoto::find($id);
            Storage::delete('public/' . $photo->path);
            $photo->delete();
        }
    }

    public function delete()
    {
        if ($this->action !== 'edit' || !$this->gallery) {
            return;
        }

        foreach ($this->gallery->photos as $photo) {
            Storage::delete('public/' . $photo->path);
            $photo->delete();
        }
        
        $this->gallery->delete();
        
        $this->dispatch('notify', type: 'success', message: 'Galería eliminada correctamente');
        $this->open = false;
        $this->dispatch('galleries:updated');
    }

    public function resetDrawer()
    {
        $this->tab_selected = 'general';
        $this->form = [
            'internal_title' => '',
            'client_title' => '',
            'internal_description' => '',
            'client_description' => '',
            'photos_to_select' => 2,
            'max_photos_to_select' => 0,
            'expiration_days' => 7,
            'download_option' => 'after_payment',
            'download_selected_only' => true,
            'session_price' => null,
            'additional_photo_price' => 5,
            'full_gallery_price' => 20,
            'watermark_enabled' => false,
            'show_filenames' => false,
            'comments_option' => 'never',
            'cash_payment_enabled' => false,
            'bank_transfer_enabled' => false,
            'payment_email_subject' => '',
            'payment_email_body' => '',
            'manual_payment_confirmation_subject' => '',
            'manual_payment_confirmation_body' => '',
            'photo_selection_confirmation_subject' => '',
            'photo_selection_confirmation_body' => '',
            'is_active' => true,
            'show_complete_session_button' => false,
            'album_20x20_photos' => null,
            'includes_canvas_60x40' => false,
        ];
        $this->photo_packages = [];
        $this->photos = [];
        $this->cover = null;
        $this->cover_key = uniqid();
        $this->photos_key = uniqid();
        $this->includedProducts = [];
        $this->gallery = null;
    }

    public function render()
    {
        return view('livewire.components.dashboard.galleries.drawer');
    }
}