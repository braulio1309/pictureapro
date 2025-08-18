<?php

namespace App\Livewire\Pages\Dashboard\Galleries;

use App\Enums\ProductType;
use App\Models\Gallery;
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

    #[On('galleries-type-switcher:change')]
    public function changeType($type)
    {
        $this->type = $type;
    }

    #[On('galleries:updated')]
    public function refreshList(?int $product_id = null)
    {
        if (!is_null($product_id)) {
            $this->dispatch('galleries:open-drawer', 'edit', $this->type, $product_id);
        }
    }

    #[Renderless]
    public function openDrawer(
        string $action = 'create',
        ?int $id = null
    ) {
        $this->dispatch('galleries:open-drawer', $action, $id);
    }

    public function render()
    {
        $galleries = Gallery::query()
            ->when(!empty($this->search), function ($query) {
                $query->where('internal_title', 'like', "%{$this->search}%");
            })
            ->with(['media'])
            ->latest()
            ->get();

        return view('livewire.pages.dashboard.galleries.index-page', compact('galleries'));
    }
}
