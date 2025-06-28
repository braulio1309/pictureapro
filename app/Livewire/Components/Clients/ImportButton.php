<?php

namespace App\Livewire\Components\Clients;

use Livewire\Component;
use Livewire\Attributes\Url;

class ImportButton extends Component
{
    #[Url]
    public $returnUrl;
    
    public $label = 'Importar';
    public $icon = 'file-import';
    public $btnClass = 'btn btn-primary';
    
    public function navigateToImport()
    {
        return  $this->redirectRoute('clients.import', navigate: true);
    }
    
    public function render()
    {
        return view('livewire.components.clients.import-button');
    }
}