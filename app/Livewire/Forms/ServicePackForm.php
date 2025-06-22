<?php

namespace App\Livewire\Forms;

use App\Models\ServicePack;
use Livewire\Form;

class ServicePackForm extends Form
{
    public ?ServicePack $pack = null;
    public int $service_id = 0;
    public string $name = '';
    public bool $is_active = true;
    public ?string $description = null;
    public ?int $duration = null;
    public ?float $price = null;
    public ?float $reservation_price = null;

    public function rules()
    {
        return [
            'service_id' => ['required', 'integer', 'exists:App\Models\Service,id'],
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'description' => ['sometimes', 'nullable', 'string'],
            'duration' => ['required', 'numeric', 'min:10'],
            'price' => ['required', 'numeric', 'min:0'],
            'reservation_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ];
    }
    public function store(): ServicePack
    {
        $data = $this->validate();
        $pack = ServicePack::create($data);

        $this->reset();

        return $pack;
    }

    public function set(ServicePack $pack): void
    {
        $this->pack = $pack;

        $this->service_id = $pack->service_id;
        $this->name = $pack->name;
        $this->is_active = $pack->is_active;
        $this->description = $pack->description;
        $this->duration = $pack->duration;
        $this->price = $pack->price;
        $this->reservation_price = $pack->reservation_price;
    }

    public function update(): ServicePack
    {
        $data = $this->validate();
        $this->pack->update($data);

        return $this->pack;
    }

    public function delete(): bool
    {
        return $this->pack->delete();
    }
}
