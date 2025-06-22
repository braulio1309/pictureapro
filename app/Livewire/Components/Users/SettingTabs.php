<?php

namespace App\Livewire\Components\Users;

use Livewire\Component;

class SettingTabs extends Component
{
    public array $tabs = [];
    public string $route = '';

    public function mount()
    {
        $this->tabs = [
            [
                'name' => 'Datos',
                'icon' => 's-user-circle',
                'route' => 'account.details',
                'active' => request()->routeIs('account.details'),
            ],
            [
                'name' => 'Tu imagen',
                'icon' => 's-identification',
                'route' => 'account.images',
                'active' => request()->routeIs('account.images'),
            ],
            [
                'name' => 'Cobros',
                'icon' => 's-currency-dollar',
                'route' => 'account.payments',
                'active' => request()->routeIs('account.payments'),
            ],
        ];
    }

    public function render()
    {
        return view('livewire.components.users.setting-tabs');
    }
}
