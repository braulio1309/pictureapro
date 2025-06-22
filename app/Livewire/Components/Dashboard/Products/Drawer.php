<?php

namespace App\Livewire\Components\Dashboard\Products;

use App\Models\Product;
use Livewire\Component;
use App\Models\Material;
use App\Enums\ProductType;
use App\Models\ProductSize;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use App\Livewire\Forms\ProductForm;

class Drawer extends Component
{
    use WithFileUploads;

    public ProductForm $form;
    public bool $open = false;
    public string $action = 'create';
    public string $tab_selected = 'general';

    public mixed $cover = null;
    public string $cover_key = '';

    public array $photos = [];
    public array $preview = [];
    public array $materials = [];
    public array $sizes = [];
    public ?int $select_size = null;

    public array $tinyMCE_settings = [
        'menubar' => false,
        'statusbar' => false,
        'plugins' => 'lists link',
        'toolbar' => 'undo redo | bold italic underline | bullist numlist | link',
        'license_key' => 'gpl',
    ];

    #[On('products:open-drawer')]
    public function open(
        string $action = 'create',
        ProductType $type = ProductType::ALBUM,
        ?int $id = null
    ): void {
        $this->action = $action;
        $this->resetDrawer();

        $this->form->has_sizes = $type !== ProductType::ALBUM;

        if ($this->action === 'edit') {
            $product = Product::query()
                ->with(['media', 'materials', 'sizes'])
                ->findOrFail($id);

            $this->cover_key = (string) $product->id;
            $this->form->set($product);

            if ($type === ProductType::ALBUM) {
                $product_materials = $product->materials->pluck('pivot')->toArray();
                foreach ($this->materialsOptions as $index => $material) {
                    foreach ($product_materials as $product_material) {
                        if (
                            // ver si es material + color
                            (
                                !is_null($product_material['material_color_id']) &&
                                $material['material_id'] === $product_material['material_id'] &&
                                $material['material_color_id'] === $product_material['material_color_id']
                            ) ||
                            // es solamente material, sin color
                            (
                                is_null($product_material['material_color_id']) &&
                                $material['material_id'] === $product_material['material_id']
                            )
                        ) {
                            $this->materials[] = $index === 0
                                ? "$index"
                                : $index;
                        }
                    }
                }
            }

            if ($type !== ProductType::ALBUM) {
                $this->sizes = $product->sizes
                    ->map(function ($size, $index) {
                        return [
                            'id' => $index + 1,
                            'width' => $size->width,
                            'height' => $size->height,
                            'price' => $size->price,
                            'min_photos' => $size->min_photos,
                            'max_photos' => $size->max_photos,
                            'product_size_id' => $size->id,
                        ];
                    })
                    ->toArray();
            }
        }

        $this->form->type = $type;
        $this->open = true;
    }

    #[Computed]
    public function coverThumbnail()
    {
        if (!is_null($this->cover)) {
            return $this->cover->temporaryUrl();
        }

        if (
            !is_null($this->form->product) &&
            !is_null($this->form->product->getFirstMedia('cover'))
        ) {
            return $this->form->product->getFirstMediaUrlCustom('cover');
        }

        return asset('images/placeholder.webp');
    }

    #[Computed]
    public function photosThumbnails(): array
    {
        $photos = [];

        if (!is_null($this->form->product)) {
            $product_photos = $this->form->product->getMedia('photos')
                ->map(function($media) {
                    return [
                        'id' => (string) $media->id,
                        'url' => $media->getFullUrl(),
                        'from' => 'product',
                    ];
                })
                ->toArray();

            $photos = array_merge($photos, $product_photos);
        }

        if (!empty($this->photos)) {
            $file_photos = array_map(function($photo) {
                return [
                    'id' => $photo->getFilename(),
                    'url' => $photo->temporaryUrl(),
                    'from' => 'input',
                ];
            }, $this->photos);

            $photos = array_merge($photos, $file_photos);
        }

        return array_values($photos);
    }

    public function submit()
    {
        $product = null;

        if ($this->action === 'create') {
            $product = $this->form->store();

            if (!is_null($this->cover)) {
                $product
                    ->addMedia($this->cover)
                    ->toMediaCollection('cover');
            }

            $this->dispatch('products:updated', $product->id);
        }

        if ($this->action === 'edit') {
            $product = $this->form->update();

            if (!is_null($this->cover)) {
                $product->clearMediaCollection('cover');
                $product
                    ->addMedia($this->cover)
                    ->toMediaCollection('cover');
            }

            $this->dispatch('products:updated');
        }

        // asociar fotos de muestra
        if (!empty($this->photos)) {
            foreach ($this->photos as $file) {
                $product
                    ->addMedia($file)
                    ->toMediaCollection('photos');
            }
        }

        // asociar materiales
        $selected_materials = array_filter(
            $this->materialsOptions,
            fn($index) => in_array($index, $this->materials),
            ARRAY_FILTER_USE_KEY
        );

        $product->materials()->detach();
        if (!empty($selected_materials)) {
            foreach ($selected_materials as $material) {
                $product->materials()->attach($material['material_id'], ['material_color_id' => $material['material_color_id']]);
            }
        }

        // si no tiene tamaños, eliminar todos los tamaños previos
        if (!$this->form->has_sizes) {
            $product->sizes()->delete();
            $this->sizes = [];
        }
        // si tiene tamaños, crearlos o actualizarlos
        if (!empty($this->sizes)) {
            foreach ($this->sizes as $size) {
                if (!is_null($size['product_size_id'])) {
                    $product->sizes()->find($size['product_size_id'])->update($size);
                    continue;
                }

                $product->sizes()->create($size);
            }
        }

        $this->resetDrawer();
        $this->open = false;
    }

    public function resetDrawer()
    {
        $this->tab_selected = 'general';
        $this->form->reset();
        $this->photos = [];
        $this->materials = [];
        $this->sizes = [];
        $this->cover = null;
        $this->cover_key = uniqid();
    }

    public function deletePhoto(string $id)
    {
        $photo = array_values(array_filter($this->photosThumbnails, fn($photo) => $photo['id'] === $id))[0];
        
        if ($photo['from'] === 'input') {
            $this->photos = array_values(array_filter($this->photos, fn($photo) => $photo->getFilename() !== $id));
        }
        
        if ($photo['from'] === 'product') {
            $this->form->product->deleteMedia($id);
        }

        unset($this->photosThumbnails);
        if ($this->action === 'edit') {
            $this->form->product->refresh();
        }
    }

    public function delete()
    {
        if ($this->action !== 'edit') {
            return;
        }

        $this->form->delete();
        $this->open = false;
        $this->dispatch('products:updated');
    }

    #[Computed]
    public function materialsOptions()
    {
        if ($this->form->type !== ProductType::ALBUM) {
            return [];
        }

        $select_materials = Material::query()
            ->with(['media', 'colors', 'colors.media'])
            ->get()
            ->map(function ($material) {
                if ($material->colors->isEmpty()) {
                    return [[
                        'material_id' => $material->id,
                        'material_color_id' => null,
                        'name' => $material->name,
                        'avatar' => $material->cover,
                    ]];
                }

                $colors = $material->colors->map(function ($color) use ($material) {
                    return [
                        'material_id' => $material->id,
                        'material_color_id' => $color->id,
                        'name' => $material->name . ' (' . $color->name . ')',
                        'avatar' => $color->cover ?? $material->cover,
                    ];
                });

                return $colors->toArray();
            })
            ->collapse()
            ->map(function ($material, $index) {
                $material['id'] = $index;
                $material['avatar'] = $material['avatar'] ?? asset('images/placeholder.webp');

                return $material;
            })
            ->toArray();

        return $select_materials;
    }

    /**
     * SIZES
     */
    public function addSize(?int $width = null, ?int $height = null)
    {
        $this->sizes[] = [
            'id' => count($this->sizes) + 1,
            'width' => $width,
            'height' => $height,
            'price' => null,
            'min_photos' => null,
            'max_photos' => null,
            'product_size_id' => null,
        ];
    }

    public function removeSize(int $index)
    {
        $size = $this->sizes[$index];

        if (!is_null($size['product_size_id'])) {
            ProductSize::query()->find($size['product_size_id'])->delete();
        }

        unset($this->sizes[$index]);
        $this->sizes = array_values($this->sizes);
    }

    #[Computed]
    public function sizesOptions(): array
    {
        return [
            ['id' => 1, 'name' => '10x15'],
            ['id' => 2, 'name' => '13x18'],
            ['id' => 3, 'name' => '15x20'],
            ['id' => 4, 'name' => '20x30'],
            ['id' => 5, 'name' => '30x40'],
        ];
    }

    public function addSizeFromOptions($id)
    {
        $size = explode('x', $this->sizesOptions[$id-1]['name']);
        $this->addSize($size[0], $size[1]);
        $this->select_size = null;
    }

    /**
     * RENDER
     */
    public function render()
    {
        return view('livewire.components.dashboard.products.drawer');
    }
}
