<?php

namespace App\Livewire\Components\Dashboard\Services;

use App\Models\Service;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Livewire\Forms\ServiceForm;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;

class Drawer extends Component
{
    use WithFileUploads;

    public bool $open = false;
    public string $action = 'create';
    public string $tab = 'general';

    public ServiceForm $form;
    public mixed $cover = null;
    public string $coverId = '';

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
    }

    #[On('services:open-drawer')]
    public function open(
        string $action = 'create',
        ?int $id = null,
        string $tab = 'general'
    ) {
        $this->action = $action;
        $this->tab = $tab;
        $this->coverId = uniqid();

        if ($this->action === 'create') {
            $this->cover = null;
            $this->form->reset();
        }

        if ($this->action === 'edit') {
            $service = Service::query()
                ->with(['media', 'packs', 'packs.media'])
                ->findOrFail($id);

            $this->form->set($service);
            $this->dispatch('content-updated', content: $service->description);
        }

        $this->open = true;
    }

    #[Computed]
    public function packs()
    {
        if ($this->action !== 'edit') {
            return [];
        }
        return $this->form->service->packs ?? [];
    }

    #[Computed]
    public function coverThumbnail()
    {
        if (!is_null($this->cover)) {
            return $this->cover->temporaryUrl();
        }

        if (
            !is_null($this->form->service) &&
            !is_null($this->form->service->getFirstMedia('cover'))
        ) {
            return $this->form->service->getFirstMediaUrlCustom('cover');
        }

        return asset('images/placeholder.webp');
    }

    #[Computed]
    public function drawerKey(): string
    {
        return uniqid();
    }

    public function changeTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function submit(): void
    {
        if ($this->action === 'create') {
            $service = $this->form->store();

            if (!is_null($this->cover)) {
                $service
                    ->addMedia($this->cover)
                    ->toMediaCollection('cover');
            }

            $this->open = false;
        }

        if ($this->action === 'edit') {
            $service = $this->form->update();

            if (!is_null($this->cover)) {
                $service->clearMediaCollection('cover');
                $service
                    ->addMedia($this->cover)
                    ->toMediaCollection('cover');
            }
        }

        $this->cover = null;
        $this->dispatch('services:updated');
    }

    public function openPackDrawer(string $action = 'create', ?int $pack_id = null)
    {
        $this->open = false;
        $this->dispatch('services:open-pack-drawer', $action, $this->form->service->id, $pack_id);
    }

    public function deletePack(int $pack_id)
    {
        $this->form->service->packs()->findOrFail($pack_id)->delete();
        $this->dispatch('services:updated');
    }

    public function delete()
    {
        if ($this->action !== 'edit') {
            return;
        }

        $this->form->delete();
        $this->open = false;
        $this->dispatch('services:updated');
    }

    public function render()
    {
        return view('livewire.components.dashboard.services.drawer');
    }
}
