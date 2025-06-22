<?php

namespace App\Livewire\Components\Dashboard\Materials;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use App\Livewire\Forms\MaterialForm;
use App\Models\Material;

class Drawer extends Component
{
    use WithFileUploads;

    public bool $open = false;
    public string $action = 'create';
    public MaterialForm $form;
    public mixed $cover = null;

    #[On('materials:open-drawer')]
    public function open(string $action = 'create', ?int $id = null): void
    {
        $this->action = $action;

        if ($this->action === 'create') {
            $this->cover = null;
            $this->form->reset();
        }

        if ($this->action === 'edit') {
            $material = Material::query()
                ->with(['media', 'colors', 'colors.media'])
                ->findOrFail($id);

            $this->form->set($material);
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
            !is_null($this->form->material) &&
            !is_null($this->form->material->getFirstMedia('cover'))
        ) {
            return $this->form->material->getFirstMediaUrlCustom('cover');
        }

        return asset('images/placeholder.webp');
    }

    public function submit()
    {
        if ($this->action === 'create') {
            $material = $this->form->store();

            if (!is_null($this->cover)) {
                $material
                    ->addMedia($this->cover)
                    ->toMediaCollection('cover');
            }

            $this->dispatch('materials:updated', $material->id);
        }

        if ($this->action === 'edit') {
            $material = $this->form->update();

            if (!is_null($this->cover)) {
                $material->clearMediaCollection('cover');
                $material
                    ->addMedia($this->cover)
                    ->toMediaCollection('cover');
            }

            $this->dispatch('materials:updated');
        }

        $this->open = false;
        $this->cover = null;
    }

    public function delete()
    {
        if ($this->action !== 'edit') {
            return;
        }

        $this->form->delete();
        $this->open = false;
        $this->dispatch('materials:updated');
    }

    public function openColorDrawer(string $action = 'create', ?int $color_id = null)
    {
        $this->open = false;
        $this->dispatch('materials:open-color-drawer', $action, $this->form->material->id, $color_id);
    }

    public function render()
    {
        return view('livewire.components.dashboard.materials.drawer');
    }
}
