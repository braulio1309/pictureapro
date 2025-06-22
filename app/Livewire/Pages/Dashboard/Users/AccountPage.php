<?php

namespace App\Livewire\Pages\Dashboard\Users;

use App\Livewire\Forms\UserForm;
use App\Models\Country;
use App\Models\Province;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class AccountPage extends Component
{
    use WithFileUploads;
    use Toast;

    public UserForm $form;
    public mixed $signature = null;

    public function mount()
    {
        $this->form->set(auth()->user());
    }

    public function removeSignature()
    {
        $this->signature = null;
        $this->form->user->clearMediaCollection('signature');
        
        return redirect()->route('account.details');
    }

    public function updateSignature()
    {
        if (is_null($this->signature)) {
            return;
        }

        $this->form->user->clearMediaCollection('signature');
        $this->form->user
            ->addMedia($this->signature)
            ->toMediaCollection('signature');
    }

    public function update()
    {
        $this->form->update();
        $this->updateSignature();

        $this->success('Información actualizada', css: 'bg-primary text-white');
    }

    public function render()
    {
        return view('livewire.pages.dashboard.users.account-page');
    }
}
