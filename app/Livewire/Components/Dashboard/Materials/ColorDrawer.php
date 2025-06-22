<?php

namespace App\Livewire\Components\Dashboard\Materials;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\MaterialColor;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use App\Livewire\Forms\MaterialColorForm;

class ColorDrawer extends Component
{
    use WithFileUploads;

    public bool $open = false;
    public string $action = 'create';
    public MaterialColorForm $form;
    public mixed $cover = null;

    #[On('materials:open-color-drawer')]
    public function open(
        string $action = 'create',
        int $material_id = 0,
        ?int $id = null
    ): void {
        $this->action = $action;

        if ($this->action === 'create') {
            $this->cover = null;
            $this->form->reset();

            $this->form->material_id = $material_id;
        }

        if ($this->action === 'edit') {
            $color = MaterialColor::query()
                ->with(['media'])
                ->findOrFail($id);

            $this->form->set($color);
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
            !is_null($this->form->color) &&
            !is_null($this->form->color->getFirstMedia('cover'))
        ) {
            return $this->form->color->getFirstMediaUrlCustom('cover');
        }

        return asset('images/placeholder.webp');
    }

    public function submit()
    {
        $material_id = $this->form->material_id;

        if ($this->action === 'create') {
            $color = $this->form->store();

            if (!is_null($this->cover)) {
                $color->addMedia($this->cover)->toMediaCollection('cover');
            }
        }

        if ($this->action === 'edit') {
            $color = $this->form->update();

            if (!is_null($this->cover)) {
                $color->clearMediaCollection('cover');
                $color
                    ->addMedia($this->cover)
                    ->toMediaCollection('cover');
            }
        }

        $this->open = false;
        $this->cover = null;

        $this->dispatch('materials:color-updated', $material_id);
    }

    public function delete()
    {
        $material_id = $this->form->material_id;

        if ($this->action !== 'edit') {
            return;
        }

        $this->form->delete();
        $this->open = false;
        $this->dispatch('materials:color-updated', $material_id);
    }

    public function back(): void
    {
        $material_id = $this->form->material_id;
        $this->open = false;
        $this->dispatch('materials:open-drawer', 'edit', $material_id);
    }

    public function render()
    {
        return view('livewire.components.dashboard.materials.color-drawer');
    }
}
