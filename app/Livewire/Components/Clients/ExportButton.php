<?php

namespace App\Livewire\Components\Clients;

use Livewire\Component;
use App\Exports\ClientsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportButton extends Component
{
    public $model;
    public $label;
    public $btnClass;
    public $tooltip;
    public $icon;
    public $badge;
    public $helpText;


    public function mount($model = 'clients', $label = 'Exportar')
    {
        $this->model = $model;
        $this->label = $label;
    }

    public function export()
    {
        $filename = $this->model . '_' . now()->format('Y-m-d') . '.csv';
        
        return Excel::download(new ClientsExport, $filename, \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render()
    {
        return view('livewire.components.clients.export-button');
    }
}