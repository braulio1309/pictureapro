<?php

namespace App\Livewire\Pages\Admin\Users;

use App\Models\User;
use Mary\Traits\Toast;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.admin')]
class IndexPage extends Component
{
    use WithPagination;
    use Toast;

    public string $search = '';
    public array $sortBy = ['column' => 'created_at', 'direction' => 'desc'];

    public function getTableHeaders(): array
    {
        return [
            // ['key' => 'id', 'label' => '#', 'class' => 'text-black'],
            ['key' => 'name', 'label' => 'Nombres', 'class' => 'text-black'],
            ['key' => 'lastname', 'label' => 'Apellidos', 'class' => 'text-black'],
            ['key' => 'email', 'label' => 'Correo', 'class' => 'text-black'],
            ['key' => 'is_enabled', 'label' => 'Habilitado', 'class' => 'text-black text-center'],
            ['key' => 'created_at', 'label' => 'Fecha de alta', 'class' => 'text-black'],
        ];
    }

    public function toggleEnabled(User $user)
    {
        $user->update(['is_enabled' => !$user->is_enabled]);
        $this->success('Usuario actualizado correctamente.', css: 'bg-primary text-white');
    }

    public function impersonate(User $user)
    {
        Auth::user()->impersonate($user);
        return redirect()->route('dashboard');
    }

    public function delete(User $user)
    {
        $user->forceDelete();
        $this->success('Usuario eliminado correctamente.', css: 'bg-primary text-white');
    }

    public function render()
    {
        $headers = $this->getTableHeaders();
        $users = User::query()
            //->role('photographer')
            ->when(!empty($this->search), function ($query) {
                $query->where(function ($query) {
                    $query
                        ->where('name', 'like', "%{$this->search}%")
                        ->orWhere('lastname', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('phone_number', 'like', "%{$this->search}%");
                });
            })
            ->orderBy(...array_values($this->sortBy))
            ->paginate(perPage: config('app.defaults.pagination'));

        return view('livewire.pages.admin.users.index-page', compact('headers', 'users'));
    }
}
