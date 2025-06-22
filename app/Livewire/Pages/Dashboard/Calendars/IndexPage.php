<?php

namespace App\Livewire\Pages\Dashboard\Calendars;

use App\Models\Calendar;
use Livewire\Component;
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

    #[On('calendars:updated')]
    public function refreshList()
    {
        //
    }

    #[Renderless]
    public function openDrawer(string $action = 'create', ?int $id = null)
    {
        $this->dispatch('calendars:open-drawer', $action, $id);
    }

    public function render()
    {
        $calendars = Calendar::query()
            ->where('is_active', $this->is_active)
            ->when(!empty($this->search), function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->with(['media'])
            ->latest()
            ->get();

        return view('livewire.pages.dashboard.calendars.index-page', compact('calendars'));
    }
}
