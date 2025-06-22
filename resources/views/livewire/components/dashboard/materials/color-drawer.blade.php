<div>
    <x-drawer
        id="material-color-drawer"
        wire:model="open"
        class="w-full lg:w-2/3"
        right
        title="Configurando color"
        separator
    >
        <x-slot:actions>
            <x-button
                label="Cerrar"
                class="btn-ghost rounded-3xl !uppercase"
                wire:click="back"
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
                <div wire:key="material-color-cover-{{ $this->form->material->id ?? '000' }}">
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
        </div>
    </x-drawer>
</div>
