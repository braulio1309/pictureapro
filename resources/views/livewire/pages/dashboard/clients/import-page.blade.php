<div>
    <div class="card shadow-sm">
        <x-header title="Importar Clientes" separator>
            <x-slot:middle class="!justify-end">
               <!-- <x-button label="Descargar Plantilla" link="/plantilla-clientes" icon="o-document-arrow-down" class="btn-primary"/>-->
            </x-slot:middle>
        </x-header>

        <x-card title="Cargar Archivo">
            @session('success')
                <x-alert icon="o-check" class="alert-success mb-4">
                    {{ session('success') }}
                </x-alert>
            @endsession

            @error('importError')
                <x-alert icon="o-exclamation-triangle" class="alert-danger mb-4">
                    {{ $message }}
                </x-alert>
            @enderror

            <form wire:submit.prevent="import" enctype="multipart/form-data">
                <x-input 
                    type="file"
                    wire:model="importFile"
                    accept=".csv,.xlsx,.xls"
                    label="Seleccionar archivo"
                    class="mb-4"
                    hint="Formatos aceptados: .csv, .xlsx, .xls (Máx. 10MB)"
                />

                @if($fileUploaded)
                    <x-button 
                        type="submit"
                        label="Importar Clientes"
                        icon="o-arrow-up-tray"
                        class="btn-primary"
                        spinner="import"
                    />
                @endif
            </form>
        </x-card>
    </div>
</div>