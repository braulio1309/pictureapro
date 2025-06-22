<?php

namespace App\Livewire\Forms;

use App\Models\Service;
use Livewire\Form;

class ServiceForm extends Form
{
    public ?Service $service = null;
    public string $name = '';
    public bool $is_active = true;
    public bool $with_reservation = false;
    public ?string $description = null;

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'with_reservation' => ['required', 'boolean'],
            'description' => ['sometimes', 'nullable', 'string'],
        ];
    }
    public function store(): Service
    {
        $data = $this->validate();
        $service = Service::create($data);

        $this->reset();

        return $service;
    }

    public function set(Service $service): void
    {
        $this->service = $service;

        $this->name = $service->name;
        $this->is_active = $service->is_active;
        $this->with_reservation = $service->with_reservation;
        $this->description = $service->description;
    }

    public function update(): Service
    {
        $data = $this->validate();
        $this->service->update($data);

        return $this->service;
    }

    public function delete(): bool
    {
        $this->service->packs()->delete();

        return $this->service->delete();
    }
}
