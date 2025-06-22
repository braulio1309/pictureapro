<div>
    <x-drawer
        id="service-pack-drawer"
        wire:model="open"
        class="w-full lg:w-2/3"
        right
        title="Configurando pack"
        separator
    >
        <div class="mt-5">
            <x-form wire:submit.prevent="submit" class="gap-8">
                <x-slot:actions>
                    <x-button
                        label="Cerrar"
                        class="btn-ghost rounded-3xl !uppercase"
                        wire:click="close"
                    />
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
                </x-slot:actions>

                <div class="space-y-5">
                    <div wire:key="service-pack-cover-{{ $this->drawerKey }}">
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
                </div>

                <div class="space-y-3">
                    <h3 class="text-lg font-medium">Descripción</h3>

                    <x-editor
                        class="rounded-3xl w-2/3 md:w-full"
                        rows="5"
                        wire:model="form.description"
                        :config="$tinyMCE_settings"
                    />

                    <div>
                        <label class="input input-bordered input-primary @error('form.price') input-error @enderror rounded-3xl flex items-center justify-between gap-2 w-full max-w-md">
                            <input
                                placeholder="Duración de la sesión"
                                class="w-full max-w-md"
                                type="number"
                                min="10"
                                wire:model="form.duration"
                                required
                            >
                            <span class="badge badge-md">minutos</span>
                        </label>
                        @error('form.price') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="text-lg font-medium">Precio y condiciones <span class="text-error">*</span></h3>

                    <div>
                        <label class="input input-bordered input-primary @error('form.price') input-error @enderror rounded-3xl flex items-center justify-between gap-2 w-full">
                            <input
                                placeholder="Precio"
                                class="w-full"
                                type="number"
                                wire:model="form.price"
                                required
                            >
                            <span class="badge badge-lg">€</span>
                        </label>
                        @error('form.price') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                    </div>

                    <label class="input input-bordered input-primary rounded-3xl flex items-center justify-between gap-2 w-full">
                        <input
                            placeholder="Valor de la reserva"
                            class="w-full"
                            type="number"
                            wire:model="form.reservation_price"
                        >
                        <span class="badge badge-lg">€</span>
                    </label>
                </div>
            </x-form>
        </div>
    </x-drawer>
</div>
