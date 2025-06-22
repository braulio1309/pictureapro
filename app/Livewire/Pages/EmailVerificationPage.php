<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.guest')]
class EmailVerificationPage extends Component
{
    public function sendVerificationEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        session()->flash('success', 'Correo de verificación enviado');
    }

    public function render()
    {
        return view('livewire.pages.email-verification-page');
    }
}
