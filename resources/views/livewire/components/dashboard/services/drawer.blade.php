<div>
    <x-drawer
        id="service-drawer"
        wire:model="open"
        class="w-full lg:w-2/3"
        right
        title="Configurando servicio"
        separator
    >
        <x-slot:actions>
            <x-button
                label="Cerrar"
                class="btn-ghost rounded-3xl !uppercase"
                wire:click="close"
            />
            @if($tab === 'general')
                @if($action === 'edit')
                    <x-button
                        label="Eliminar"
                        icon="o-trash"
                        class="btn-accent btn-outline rounded-3xl !uppercase"
                        wire:click="delete"
                        spinner="delete"
                        wire:target="cover,delete,submit"
                        wire:loading.attr="disabled"
                    />
                @endif
                <x-button
                    label="Guardar"
                    icon="o-check"
                    class="btn-primary rounded-3xl !uppercase"
                    wire:click="submit"
                    spinner="submit"
                    wire:target="cover,submit"
                    wire:loading.attr="disabled"
                />
            @endif
        </x-slot:actions>

        @if($action === 'edit')
            <livewire:components.dashboard.services.drawer-tabs :model="$tab" />
        @endif

        <div class="mt-5 {{ $tab === 'general' ? '' : 'hidden' }}">
            <x-form wire:submit.prevent="submit" class="gap-5">
                <div wire:key="service-cover-{{ $this->drawerKey }}">
                    <x-file
                        label="Portada"
                        wire:model="cover"
                        change-text="Seleccionar imagen"
                        accept="image/png, image/jpeg"
                        class="w-2/3 md:w-full max-w-full"
                    >
                        <img src="{{ $this->coverThumbnail }}" alt="Portada" class="w-60 rounded-3xl">
                    </x-file>
                    <p wire:loading wire:target="cover" class="text-sm text-gray-700 italic">Subiendo imagen...</p>
                </div>

                <x-input
                    label="Titulo"
                    class="rounded-3xl max-w-full"
                    wire:model="form.name"
                    required
                />
        
                <x-toggle
                    label="Activo"
                    class="rounded-3xl"
                    wire:model="form.is_active"
                />

                <x-toggle
                    label="Reservar con fecha"
                    class="rounded-3xl"
                    wire:model="form.with_reservation"
                />

                <x-editor
                    label="Descripción"
                    class="rounded-3xl w-2/3 md:w-full"
                    wire:model="form.description"
                    :config="$tinyMCE_settings"
                />
            </x-form>
        </div>

        <div class="mt-5 {{ $tab === 'packs' ? '' : 'hidden' }}">
            <ul class="space-y-3">
                @foreach ($this->packs as $pack)
                    <li class="p-5 rounded-3xl shadow-lg flex justify-between items-center">
                        <span class="font-medium">{{ $pack->name }}</span>
                        <div class="space-x-1">
                            <x-button
                                icon="s-pencil"
                                class="btn-circle btn-ghost text-primary rounded-3xl btn-sm"
                                wire:click="openPackDrawer('edit', {{ $pack->id }})"
                            />
                            <x-button
                                icon="s-trash"
                                class="btn-circle btn-ghost text-accent rounded-3xl btn-sm"
                                wire:click="deletePack({{ $pack->id }})"
                            />
                        </div>
                    </li>
                @endforeach
            </ul>

            <x-button
                label="Agregar"
                icon="o-plus"
                class="btn-primary btn-outline rounded-3xl !uppercase mt-5"
                wire:click="openPackDrawer"
            />
        </div>
    </x-drawer>
</div>
