<?php

namespace App\Livewire\Pages;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

#[Layout('components.layouts.guest')]
class ResetPasswordPage extends Component
{
    public bool $showPassword = false;
    public string $token = '';
    #[Url]
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

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

    public function resetPassword()
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
     
                $user->save();
     
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('success', __($status));

        } else {
            session()->flash('error', __($status));
        }
    }

    public function render()
    {
        return view('livewire.pages.reset-password-page');
    }
}
