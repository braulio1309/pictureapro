<?php

namespace App\Livewire\Components\Dashboard\Clients;

use App\Livewire\Forms\ClientForm;
use App\Models\Client;
use App\Models\User;
use Mary\Traits\Toast;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class Drawer extends Component
{
    use Toast;

    public bool $open = false;
    public string $action = 'create';
    public ClientForm $form;

    public array $tinyMCE_settings = [
        'menubar' => false,
        'statusbar' => false,
        'plugins' => 'lists link',
        'toolbar' => 'undo redo | bold italic underline | bullist numlist | link',
        'license_key' => 'gpl',
    ];

    /**
     * methods
     */
    #[On('open-drawer')]
    public function open(string $action = 'create', ?int $id = null)
    {
        $this->action = $action;
        $this->form->reset();

        if ($this->action === 'create') {
            $this->form->tenant_id = Auth::id();
        }

        if ($this->action === 'edit') {
            $client = Client::query()
                ->where('tenant_id', Auth::id())
                ->findOrFail($id);

            $this->form->set($client);
        }

        $this->open = true;
    }

    public function submit(): void
    {
        if ($this->action === 'create') {
            $client = $this->form->store();

            $this->open = false;
            $this->success('Cliente creado correctamente', css: 'bg-primary text-white');
        }

        if ($this->action === 'edit') {
            $client = $this->form->update();

            $this->open = false;
            $this->success('Cliente actualizado correctamente', css: 'bg-primary text-white');
        }

        $this->dispatch('clients:updated');
    }

    public function delete()
    {
        $this->form->delete();
        $this->open = false;

        $this->success('Cliente eliminado correctamente', css: 'bg-primary text-white');
        return redirect()->route('dashboard.clients.index');
    }

    /**
     * render
     */
    public function render()
    {
        return view('livewire.components.dashboard.clients.drawer');
    }
}
