<?php

namespace App\Livewire\Pages;

use App\Models\User;
use Livewire\Component;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Renderless;

#[Layout('components.layouts.guest')]
class LoginPage extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $showPassword = false;

    public function login(Request $request)
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $this->email)->first();

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
 
            if ($user->is_enabled === false) {
                Auth::logout();

                return redirect()
                    ->route('login')
                    ->with('error', 'Tu cuenta ha sido deshabilitada');
            }

            if ($user->hasRole('admin')) {
                return redirect()->route('admin.users.index');
            }

            return redirect()->route('dashboard');
        }
 
        session()->flash('error', 'Credenciales incorrectas');
    }

    public function togglePasswordVisibility()
    {
        $this->showPassword = !$this->showPassword;
    }

    #[Computed]
    public function passwordType()
    {
        return $this->showPassword ? 'text' : 'password';
    }

    #[Computed]
    public function passwordIcon()
    {
        return $this->showPassword ? 's-eye-slash' : 's-eye';
    }

    public function render()
    {
        return view('livewire.pages.login-page');
    }
}
