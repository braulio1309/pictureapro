<?php

namespace App\Livewire\Pages\Dashboard\Services;

use App\Models\Service;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class IndexPage extends Component
{
    public string $search = '';
    public bool $is_active = true;

    #[On('active-switcher:change')]
    public function changeStatus(bool $status)
    {
        $this->is_active = $status;
    }

    #[Renderless]
    public function openDrawer(
        string $action = 'create',
        ?int $id = null
    ) {
        $this->dispatch('services:open-drawer', $action, $id);
    }

    #[On('services:updated')]
    public function refreshList()
    {}

    #[On('services:pack-updated')]
    public function openDrawerOnPacks(int $service_id)
    {
        $this->dispatch('services:open-drawer', 'edit', $service_id, 'packs');
    }

    public function render()
    {
        $services = Service::query()
            ->where('is_active', $this->is_active)
            ->when(!empty($this->search), function ($query) {
                $query->where(function ($query) {
                    $query
                        ->where('name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->with(['media'])
            ->withCount('packs')
            ->latest()
            ->get();

        return view('livewire.pages.dashboard.services.index-page', compact('services'));
    }
}
