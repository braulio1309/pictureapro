<div>
    <!-- HEADER -->
    <x-header title="Clientes" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input
                placeholder="Buscar..."
                wire:model.live.debounce.750ms="search"
                clearable
                icon="o-magnifying-glass"
                class="rounded-3xl" />

        </x-slot:middle>


    </x-header>

    <div class="flex items-center space-x-4 mb-6">
        <livewire:components.clients.export-button
            :filters="['search' => $search]"
            :label="'Exportar Resultados'"
            icon="file-excel"
            btn-class="btn btn-success" />

        <x-button
            label="Importar clientes"
            class="btn btn-secondary"
            wire-navigate
            link="{{ route('clients.import') }}" />
    </div>

    <x-select class='mb-3'
        wire:model.live.debounce.750ms="selectedService"
        placeholder="Filtrar por servicio"
        :options="[
        ...$services->map(fn($service) => [
            'value' => $service->id,
            'label' => $service->name
        ])->toArray()
    ]"
        option-value="value"
        option-label="label"
        class="rounded-3xl w-64"
        clearable />
    <!-- TABLE  -->
    <x-card class="bg-white shadow-xl rounded-3xl">
        <x-table
            :headers="$headers"
            :rows="$clients"
            :sort-by="$sortBy"
            with-pagination
            show-empty-text
            empty-text="No hay resultados">
            @scope('cell_created_at', $client)
            {{ $client['created_at']->format('d/m/Y') }}
            @endscope

            @scope('actions', $client)
            <div class="flex flex-row space-x-1">
                <x-button
                    label="Editar"
                    class="btn-sm btn-primary rounded-3xl"
                    @click="$dispatch('open-drawer', { action: 'edit', id: {{ $client['id'] }} })" />
                <x-button
                    label="Detalles"
                    class="btn-sm btn-secondary rounded-3xl"
                    wire-navigate
                    link="{{ route('dashboard.clients.show', $client['id']) }}" />
                <x-button
                    label="Eliminar"
                    class="btn-sm btn-accent rounded-3xl"
                    wire:click="delete({{ $client['id'] }})" />
            </div>
            @endscope
        </x-table>
    </x-card>

    <livewire:components.dashboard.open-drawer-button />
    <livewire:components.dashboard.clients.drawer />
</div>