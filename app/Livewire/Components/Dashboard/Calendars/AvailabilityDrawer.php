<?php

namespace App\Livewire\Components\Dashboard\Calendars;

use Mary\Traits\Toast;
use Livewire\Component;
use App\Models\ServicePack;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use App\Models\CalendarAvailability;
use App\Livewire\Forms\CalendarAvailabilityForm;

class AvailabilityDrawer extends Component
{
    use Toast;

    public CalendarAvailabilityForm $form;
    public bool $open = false;
    public string $action = 'create';

    public function resetDrawer()
    {
        $this->form->reset();
    }

    #[On('calendars:open-availability')]
    public function open(string $action = 'create', ?string $id = null): void
    {
        $this->action = $action;
        $this->resetDrawer();

        if ($this->action === 'edit') {
            $availability = CalendarAvailability::query()
                ->with(['calendar', 'calendar.services', 'packs'])
                ->findOrFail($id);

            $this->form->set($availability);
        }

        $this->open = true;
    }

    public function submit()
    {
        if ($this->action === 'edit') {
            $availability = $this->form->update();

            $this->dispatch('calendars:availability-updated');
            $this->success('Cita actualizada correctamente', css: 'bg-primary text-white');
        }

        $this->open = false;
        $this->resetDrawer();
    }

    public function delete()
    {
        if ($this->action !== 'edit') {
            return;
        }

        $this->form->delete();

        $this->open = false;
        $this->resetDrawer();
        $this->dispatch('calendars:availability-updated');
        $this->success('Cita eliminada correctamente', css: 'bg-primary text-white');
    }

    /**
     * render
     */
    #[Computed]
    public function packs()
    {
        if (is_null($this->form->availability)) {
            return collect();
        }

        $services = $this->form->availability->calendar->services->pluck('id')->toArray();

        $packs = ServicePack::query()
            ->select('id', 'service_id', 'name')
            ->where('is_active', true)
            ->whereIn('service_id', $services)
            ->with(['service', 'media'])
            ->get()
            ->map(fn($pack) => (object) [
                'id' => $pack->id,
                'name' => $pack->service->name . ' - '. $pack->name,
                'avatar' => $pack->cover ?? asset('images/placeholder.webp'),
            ]);

        return $packs;
    }


    public function render()
    {
        return view('livewire.components.dashboard.calendars.availability-drawer');
    }
}
