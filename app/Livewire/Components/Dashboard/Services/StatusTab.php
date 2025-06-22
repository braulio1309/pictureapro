<?php

namespace App\Livewire\Components\Dashboard\Services;

use Livewire\Component;

class StatusTab extends Component
{
    public mixed $value = 1;

    private function getStatuses(): array
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

    public function emitChangeStatus($value)
    {
        $this->value = $value;
        $this->dispatch('services:change-status', $value);
    }

    public function render()
    {
        $tabs = $this->getStatuses();

        return view('livewire.components.dashboard.services.status-tab', compact('tabs'));
    }
}
