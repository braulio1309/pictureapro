<?php

namespace App\Livewire\Forms;

use App\Events\CalendarServicesUpdated;
use App\Models\Calendar;
use Livewire\Form;

class CalendarForm extends Form
{
    public ?Calendar $calendar = null;
    public string $name = '';
    public string $slug = '';
    public ?string $description = null;
    public bool $is_active = true;
    public array $services = [];
    public bool $show_busy = false;
    public bool $require_address = false;
    public bool $require_nif_document = false;
    public bool $require_assistants = false;

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'description' => ['sometimes', 'nullable', 'string'],
            'services' => ['sometimes', 'array'],
            'services.*' => ['integer', 'exists:App\Models\Service,id'],
            'show_busy' => ['sometimes', 'boolean'],
            'require_address' => ['sometimes', 'boolean'],
            'require_nif_document' => ['sometimes', 'boolean'],
            'require_assistants' => ['sometimes', 'boolean'],
        ];
    }

    public function store(): Calendar
    {
        $data = $this->validate();
        $data['slug'] = uniqid();

        $calendar = Calendar::create($data);
        $this->reset();

        return $calendar;
    }

    public function set(Calendar $calendar): void
    {
        $this->calendar = $calendar;

        $this->name = $calendar->name;
        $this->slug = $calendar->slug;
        $this->is_active = $calendar->is_active;
        $this->description = $calendar->description;
        $this->show_busy = $calendar->show_busy;
        $this->require_address = $calendar->require_address;
        $this->require_nif_document = $calendar->require_nif_document;
        $this->require_assistants = $calendar->require_assistants;
        
        if ($calendar->relationLoaded('services')) {
            $services = $calendar->services->map(fn($service) => $service->id)->toArray();
            $this->services = $services;
        }
    }

    public function update(): Calendar
    {
        $data = $this->validate();
        $this->calendar->update($data);

        if (!is_null($this->services)) {
            $this->calendar->services()->sync($this->services);
            event(new CalendarServicesUpdated($this->calendar->id));
        }

        $this->calendar->refresh();

        return $this->calendar;
    }

    public function delete(): bool
    {
        $this->calendar->media()->delete();
        $this->calendar->services()->detach();
        $this->calendar->availabilities()->delete();

        return $this->calendar->delete();
    }
}
