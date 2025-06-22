<?php

namespace App\Livewire\Components\Dashboard\Services;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class DrawerTabs extends Component
{
    #[Reactive]
    public string $model = 'general';

    #[Computed]
    public function tabs(): array
    {
        return [
            [
                'name' => 'General',
                'icon' => 's-building-storefront',
                'value' => 'general',
                'active' => $this->model === 'general',
            ],
            [
                'name' => 'Packs',
                'icon' => 'o-shopping-bag',
                'value' => 'packs',
                'active' => $this->model === 'packs',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.components.dashboard.services.drawer-tabs');
    }
}
