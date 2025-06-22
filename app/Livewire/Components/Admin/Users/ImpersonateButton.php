<?php

namespace App\Livewire\Components\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

class ImpersonateButton extends Component
{
    public User $user;
    public string $classes = '';

    public function impersonate()
    {
        Auth::user()->impersonate($this->user);
        return redirect()->route('dashboard');
    }

    #[Computed]
    public function icon(): string
    {
        if (str_contains($this->classes, 'btn-sm')) {
            return '';
        }

        return 'o-identification';
    }

    public function render()
    {
        return view('livewire.components.admin.users.impersonate-button');
    }
}
