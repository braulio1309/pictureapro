<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Password;

#[Layout('components.layouts.guest')]
class ForgotPasswordPage extends Component
{
    public string $email = '';

    public function sendResetEmail()
    {
        $this->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink([
            'email' => $this->email
        ]);
     
        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('success', __($status));
        } else {
            session()->flash('error', __($status));
        }
    }

    public function render()
    {
        return view('livewire.pages.forgot-password-page');
    }
}
