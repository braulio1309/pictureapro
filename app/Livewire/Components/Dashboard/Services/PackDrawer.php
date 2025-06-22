<?php

namespace App\Livewire\Components\Dashboard\Services;

use App\Models\Service;
use Livewire\Component;
use App\Models\ServicePack;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use App\Livewire\Forms\ServicePackForm;

class PackDrawer extends Component
{
    use WithFileUploads;

    public bool $open = false;
    public string $action = 'create';
    public ServicePackForm $form;
    public mixed $cover = null;

    public ?Service $service = null;

    public array $tinyMCE_settings = [
        'menubar' => false,
        'statusbar' => false,
        'plugins' => 'lists link',
        'toolbar' => 'undo redo | bold italic underline | bullist numlist | link',
        'license_key' => 'gpl',
    ];

    #[Renderless]
    public function close()
    {
        $this->open = false;
        $this->cover = null;
        $this->dispatch('services:pack-updated', $this->form->service_id);
    }

    #[On('services:open-pack-drawer')]
    public function open(
        string $action = 'create',
        int $service_id = 0,
        ?int $id = null
    ) {
        $this->action = $action;

        if ($this->action === 'create') {
            $this->cover = null;
            $this->form->reset();

            $this->form->service_id = $service_id;
        }

        if ($this->action === 'edit') {
            $pack = ServicePack::findOrFail($id);
            $this->form->set($pack);
        }

        $this->open = true;
    }

    #[Computed]
    public function coverThumbnail()
    {
        if (!is_null($this->cover)) {
            return $this->cover->temporaryUrl();
        }

        if (
            !is_null($this->form->pack) &&
            !is_null($this->form->pack->getFirstMedia('cover'))
        ) {
            return $this->form->pack->getFirstMediaUrlCustom('cover');
        }

        return asset('images/placeholder.webp');
    }

    #[Computed]
    public function drawerKey(): string
    {
        return uniqid();
    }

    public function submit()
    {
        $service_id = $this->form->service_id;

        if ($this->action === 'create') {
            $pack = $this->form->store();

            if (!is_null($this->cover)) {
                $pack->addMedia($this->cover)->toMediaCollection('cover');
            }
        }

        if ($this->action === 'edit') {
            $pack = $this->form->update();

            if (!is_null($this->cover)) {
                $pack->clearMediaCollection('cover');
                $pack->addMedia($this->cover)->toMediaCollection('cover');
            }
        }

        $this->open = false;
        $this->cover = null;

        $this->dispatch('services:pack-updated', $service_id);
    }

    public function delete()
    {
        $service_id = $this->form->service_id;

        if ($this->action !== 'edit') {
            return;
        }

        $this->form->delete();
        $this->open = false;
        $this->dispatch('services:pack-updated', $service_id);
    }

    public function render()
    {
        return view('livewire.components.dashboard.services.pack-drawer');
    }
}
