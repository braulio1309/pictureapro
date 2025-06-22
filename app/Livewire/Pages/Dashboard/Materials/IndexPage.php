<?php

namespace App\Livewire\Pages\Dashboard\Materials;

use Livewire\Component;
use App\Models\Material;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;

class IndexPage extends Component
{
    public string $search = '';
    public bool $is_active = true;

    #[On('active-switcher:change')]
    public function changeStatus(bool $status)
    {
        $this->is_active = $status;
    }

    #[On('materials:updated')]
    public function refreshList(?int $material_id = null)
    {
        if (!is_null($material_id)) {
            $this->dispatch('materials:open-drawer', 'edit', $material_id);
        }
    }

    #[Renderless]
    public function openDrawer(
        string $action = 'create',
        ?int $id = null
    ) {
        $this->dispatch('materials:open-drawer', $action, $id);
    }

    #[On('materials:color-updated')]
    public function openDrawerOnPacks(int $material_id)
    {
        $this->dispatch('materials:open-drawer', 'edit', $material_id);
    }

    public function render()
    {
        $materials = Material::query()
            ->where('is_active', $this->is_active)
            ->when(!empty($this->search), function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->with(['media'])
            ->withCount('colors')
            ->latest()
            ->get();

        return view('livewire.pages.dashboard.materials.index-page', compact('materials'));
    }
}
