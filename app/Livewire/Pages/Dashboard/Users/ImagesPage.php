<?php

namespace App\Livewire\Pages\Dashboard\Users;

use Mary\Traits\Toast;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Livewire\Forms\UserForm;

class ImagesPage extends Component
{
    use WithFileUploads;
    use Toast;

    public UserForm $form;
    public mixed $logo = null;
    public mixed $watermark_vertical = null;
    public mixed $watermark_horizontal = null;

    public function mount()
    {
        $this->form->set(auth()->user());
    }

    public function updateFile(string $name)
    {
        if (is_null($this->{$name})) {
            return;
        }

        $this->form->user->clearMediaCollection($name);
        $this->form->user
            ->addMedia($this->{$name})
            ->toMediaCollection($name);
    }

    public function removeFile(string $name)
    {
        $this->{$name} = null;
        $this->form->user->clearMediaCollection($name);
        
        return redirect()->route('account.images');
    }

    public function update()
    {
        $this->updateFile('logo');
        $this->updateFile('watermark_vertical');
        $this->updateFile('watermark_horizontal');

        $this->success('Imagenes actualizadas', css: 'bg-primary text-white');
    }

    public function render()
    {
        return view('livewire.pages.dashboard.users.images-page');
    }
}
