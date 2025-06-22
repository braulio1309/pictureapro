<?php

namespace App\Livewire\Pages;

use App\Models\Plan;
use App\Models\Role;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Livewire\Forms\UserForm;
use App\Jobs\Users\AssignPlanToUser;
use App\Jobs\Users\AssignRoleToUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

#[Layout('components.layouts.guest')]
class RegisterPage extends Component
{
    public UserForm $form;

    public function register()
    {
        $user = $this->form->store();
        
        event(new Registered($user));
        dispatch(new AssignRoleToUser($user->id, Role::PHOTOGRAPHER));
        dispatch(new AssignPlanToUser($user->id, Plan::BASIC));

        Auth::login($user);

        return redirect()
            ->route('login')
            ->with('success', 'Cuenta registrada correctamente');
    }

    public function render()
    {
        return view('livewire.pages.register-page');
    }
}
