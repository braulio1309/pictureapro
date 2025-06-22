<div>
    <!-- HEADER -->
    <x-header title="Usuarios" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input
                placeholder="Buscar..."
                wire:model.live.debounce.750ms="search"
                clearable
                icon="o-magnifying-glass" 
                class="rounded-3xl"
            />
        </x-slot:middle>
    </x-header>

    <!-- TABLE  -->
    <x-card class="bg-white shadow-xl rounded-3xl">
        <x-table
            :headers="$headers"
            :rows="$users"
            :sort-by="$sortBy"
            with-pagination
            show-empty-text
            empty-text="No hay resultados"
        >
            @scope('cell_is_enabled', $user)
                {{ $user['is_enabled'] ? '✅' : '❌' }}
            @endscope

            @scope('cell_created_at', $user)
                {{ $user['created_at']->format('d/m/Y') }}
            @endscope

            @scope('actions', $user)
                <div class="flex flex-row space-x-1">
                    <x-button
                        label="Detalles"
                        class="btn-sm btn-primary rounded-3xl"
                        wire-navigate
                        link="{{ route('admin.users.show', $user['id']) }}"
                    />
                    <x-button
                        label="{{ $user['is_enabled'] ? 'Desactivar' : 'Activar' }}"
                        wire:click="toggleEnabled({{ $user['id'] }})"
                        class="{{ $user['is_enabled'] ? 'btn-accent' : 'btn-primary' }} btn-outline btn-sm text-white rounded-3xl"
                    />
                    <x-button
                        label="{{ __('Impersonate') }}"
                        wire:click="impersonate({{ $user['id'] }})"
                        class="btn-sm rounded-3xl"
                    />
                    <x-button
                        label="Eliminar"
                        class="btn-sm btn-accent rounded-3xl"
                        wire:click="delete({{ $user['id'] }})"
                    />
                </div>
            @endscope
        </x-table>
    </x-card>
</div>
