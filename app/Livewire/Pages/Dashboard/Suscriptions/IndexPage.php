<?php

namespace App\Livewire\Pages\Dashboard\Suscriptions;

use Livewire\Component;

class Plans extends Component
{
    public array $plans = [];

    public function mount()
    {
        $this->plans = config('plans.plans', []);
    }

    public function render()
    {
        return view('livewire.subscriptions.plans');
    }
}
