<?php

namespace App\Livewire\Pages\Dashboard\Clients;

use App\Livewire\Forms\ClientForm;
use App\Models\Client;
use App\Models\Service;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;

class IndexPage extends Component
{
    use WithPagination;
    use Toast;

    public ClientForm $form;
    public string $search = '';
    public string $permisos = '';
    public string $selectedService = '';

    public array $sortBy = ['column' => 'created_at', 'direction' => 'desc'];

    /**
     * actions
     */
    public function delete(int $id): void
    {
        $client = Client::query()
            ->where('tenant_id', Auth::id())
            ->findOrFail($id);

        $this->form->set($client);
        $this->form->delete();
        
        $this->success('Cliente eliminado correctamente', css: 'bg-primary text-white');
    }

    /**
     * renders
     */
    public function getTableHeaders(): array
    {
        return [
            // ['key' => 'id', 'label' => '#', 'class' => 'text-black'],
            ['key' => 'name', 'label' => 'Nombres', 'class' => 'text-black'],
            ['key' => 'lastname', 'label' => 'Apellidos', 'class' => 'text-black'],
            ['key' => 'email', 'label' => 'Correo', 'class' => 'text-black'],
            ['key' => 'created_at', 'label' => 'Fecha de alta', 'class' => 'text-black'],
        ];
    }

    #[On('clients:updated')]
    #[On('clients:deleted')]
    public function render()
    {
        
        $headers = $this->getTableHeaders();
        $clients = Client::query()
            ->where('tenant_id', Auth::id())
            ->when(!empty($this->search), function ($query) {
                $query->where(function ($query) {
                    $query
                        ->where('name', 'like', "%{$this->search}%")
                        ->orWhere('lastname', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('phone_number', 'like', "%{$this->search}%");
                });
            })
            ->when(!empty($this->selectedService), function ($query) {
                $query->whereHas('bookings.pack.service', function ($q) {
                    $q->where('service_id', (int)$this->selectedService);
                });
            })
            ->when(!empty($this->permisos), function ($query) {
                $query->where(function ($q) {
                    if ($this->permisos == 1){
                        $q->where('allow_publish_images', true);
                    }else if ($this->permisos == 2) {
                        $q->where('allow_commercial_comms', true);
                    }else if ($this->permisos == 3){
                        $q->where('allow_commercial_comms', true)
                            ->where('allow_publish_images', true);
                    }else {
                        $q->where('allow_commercial_comms', false)
                            ->where('allow_publish_images', false);
                    }
                });
            })
            ->orderBy(...array_values($this->sortBy))
            ->paginate(perPage: config('app.defaults.pagination'));


        $services = Service::where('tenant_id', '=', Auth::id())->get();

        return view('livewire.pages.dashboard.clients.index-page', compact('headers', 'clients', 'services'));
    }
}
