<div>
    <x-drawer
        id="material-drawer"
        wire:model="open"
        class="w-full lg:w-2/3"
        right
        title="Configurando material"
        separator
    >
        <x-slot:actions>
            <x-button
                label="Cerrar"
                class="btn-ghost rounded-3xl !uppercase"
                @click="$wire.open = false"
            />
            @if($action === 'edit')
                <x-button
                    label="Eliminar"
                    icon="o-trash"
                    class="btn-accent btn-outline rounded-3xl !uppercase"
                    wire:click="delete"
                    spinner="delete"
                />
            @endif
            <x-button
                label="Guardar"
                icon="o-check"
                class="btn-primary rounded-3xl !uppercase"
                wire:click="submit"
                spinner="submit"
            />
        </x-slot:actions>

        <div class="mt-5">
            <x-form wire:submit.prevent="submit" class="gap-5">
                <div wire:key="material-cover-{{ $this->form->material->id ?? '000' }}">
                    <x-file
                        label="Portada"
                        wire:model="cover"
                        change-text="Seleccionar imagen"
                        accept="image/png, image/jpeg"
                        class="w-2/3 md:w-full max-w-full"
                    >
                        <img src="{{ $this->coverThumbnail }}" alt="Portada" class="w-60 rounded-3xl">
                    </x-file>
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
            </x-form>

            @if($action === 'edit')           
                <div class="mt-8">
                    <x-button
                        label="Agregar color"
                        icon="o-plus"
                        class="btn-primary rounded-3xl !uppercase"
                        wire:click="openColorDrawer"
                    />
                </div>

                <div class="my-5 grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach ($this->form->material->colors as $color)
                        <div class="col-span-1">
                            <x-card class="shadow hover:scale-105 transition-all">
                                <p class="font-medium">{{ $color->name }}</p>
                                <p
                                    class="badge badge-{{ $color->is_active ? 'secondary' : 'accent' }} text-white"
                                >{{ $color->is_active ? 'Activo' : 'Inactivo' }}</p>
                            
                                <x-slot:figure>
                                    <img src="{{ $color->getFirstMediaUrlCustom('cover') ?? asset('images/placeholder.webp') }}" />
                                </x-slot:figure>
                                <x-slot:actions>
                                    <x-button
                                        label="Editar"
                                        class="btn-primary btn-outline btn-sm rounded-3xl"
                                        wire:click="openColorDrawer('edit', {{ $color->id }})" 
                                    />
                                </x-slot:actions>
                            </x-card>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </x-drawer>
</div>
