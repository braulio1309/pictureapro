<?php

namespace App\Livewire\Pages\Dashboard\Products;

use App\Enums\ProductType;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;

class IndexPage extends Component
{
    public string $search = '';
    public bool $is_active = true;
    public ProductType $type = ProductType::ALBUM;

    #[On('active-switcher:change')]
    public function changeStatus(bool $status)
    {
        $this->is_active = $status;
    }

    #[On('products-type-switcher:change')]
    public function changeType(ProductType $type)
    {
        $this->type = $type;
    }

    #[On('products:updated')]
    public function refreshList(?int $product_id = null)
    {
        if (!is_null($product_id)) {
            $this->dispatch('products:open-drawer', 'edit', $this->type, $product_id);
        }
    }

    #[Renderless]
    public function openDrawer(
        string $action = 'create',
        ?int $id = null
    ) {
        $this->dispatch('products:open-drawer', $action, $this->type, $id);
    }

    public function render()
    {
        $products = Product::query()
            ->where('is_active', $this->is_active)
            ->where('type', $this->type)
            ->when(!empty($this->search), function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->with(['media'])
            ->latest()
            ->get();

        return view('livewire.pages.dashboard.products.index-page', compact('products'));
    }
}
