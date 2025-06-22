<?php

namespace App\Livewire\Pages\Admin\Users;

use App\Models\User;
use Mary\Traits\Toast;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class ShowPage extends Component
{
    use Toast;

    public int $id;
    public ?User $user = null;

    public function mount()
    {
        $this->user = User::query()
            ->with(['province', 'country'])
            ->findOrFail($this->id);
    }

    #[On('user:toggle-enabled')] 
    public function showSuccessToast()
    {
        $this->success('Usuario actualizado correctamente', css: 'bg-primary text-white');
    }

    public function getItems(): array
    {
        return [
            ['label' => 'Nombres', 'value' => $this->user->name],
            ['label' => 'Apellidos', 'value' => $this->user->lastname],
            ['label' => 'Correo', 'value' => $this->user->email],
            ['label' => 'Habilitado', 'value' => $this->user->is_enabled ? '✅' : '❌'],
            ['label' => 'Teléfono', 'value' => $this->user->phone_number ?? 'Sin definir'],
            ['label' => 'Dirección', 'value' => $this->user->address ?? 'Sin definir'],
            ['label' => 'Ciudad', 'value' => $this->user->city_name ?? 'Sin definir'],
            ['label' => 'Provincia', 'value' => $this->user->province_name ?? 'Sin definir'],
            ['label' => 'País', 'value' => $this->user->country_name ?? 'Sin definir'],
            ['label' => 'Código Postal', 'value' => $this->user->postal_code ?? 'Sin definir'],
            ['label' => 'Creado el', 'value' => $this->user->created_at->format('d/m/Y')],
        ];
    }

    public function render()
    {
        $items = $this->getItems();

        return view('livewire.pages.admin.users.show-page', compact('items'));
    }
}
