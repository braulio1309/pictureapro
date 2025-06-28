<?php
namespace App\Livewire\Pages\Dashboard\Clients;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Imports\ClientsImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportPage extends Component
{
    use WithFileUploads;

    public $importFile = null; // Inicializa como null
    public $importSuccess = false;
    public $importErrors = [];
    public $fileUploaded = false;

    protected $rules = [
        'importFile' => 'required|file|mimes:csv,xlsx,xls|max:10240'
    ];

    protected $messages = [
        'importFile.required' => 'Por favor seleccione un archivo válido',
        'importFile.mimes' => 'Solo se permiten archivos CSV, XLSX o XLS',
        'importFile.max' => 'El archivo no debe exceder 10MB'
    ];

    public function updatedImportFile()
    {
        $this->validateOnly('importFile'); // Validación reactiva
        $this->fileUploaded = true;
    }

    public function import()
    {
        $this->validate(); // Validación final
        
        try {
            // Verificación adicional
            if (!$this->importFile) {
                throw new \Exception('No se pudo cargar el archivo');
            }

            Excel::import(new ClientsImport, $this->importFile->getRealPath());
            
            $this->resetExcept(['importSuccess']);
            $this->importSuccess = true;
            session()->flash('success', 'Importación completada correctamente!');
            
        } catch (\Exception $e) {
            $this->addError('importError', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.dashboard.clients.import-page');
    }
}