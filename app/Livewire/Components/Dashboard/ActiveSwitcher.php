<?php

namespace App\Livewire\Components\Dashboard;

use Livewire\Attributes\Computed;
use Livewire\Component;

class ActiveSwitcher extends Component
{
    public mixed $value = 1;

    #[Computed]
    public function statuses(): array
    {
        return [
            [
                'name' => 'Activos',
                'icon' => 'o-check',
                'value' => 1,
                'active' => $this->value == 1,
            ],
            [
                'name' => 'Inactivos',
                'icon' => 'o-no-symbol',
                'value' => 0,
                'active' => $this->value == 0,
            ],
        ];
    }

    public function emitChange($value)
    {
        $this->value = $value;
        $this->dispatch('active-switcher:change', $value);
    }

    public function render()
    {
        return view('livewire.components.dashboard.active-switcher');
    }
}
