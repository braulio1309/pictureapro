<?php

namespace App\Livewire\Components\Dashboard\Products;

use App\Enums\ProductType;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TypeSwitcher extends Component
{
    public string $value = 'album';

    #[Computed]
    public function types(): array
    {
        return array_map(function ($type) {
            return [
                'name' => $type['value'],
                'icon' => $type['icon'],
                'value' => $type['id']->value,
                'active' => $this->value === $type['id']->value,
            ];
        }, ProductType::getOptions());
    }

    public function emitChange($value)
    {
        $this->value = $value;
        $this->dispatch('products-type-switcher:change', $value);
    }

    public function render()
    {
        return view('livewire.components.dashboard.products.type-switcher');
    }
}
