<div>
    <x-drawer
        id="client-drawer"
        wire:model="open"
        class="w-full lg:w-2/3"
        right
        title="Configurando cliente"
        separator>
        <x-slot:actions>
            <x-button
                label="Cerrar"
                class="btn-ghost rounded-3xl !uppercase"
                @click="$wire.open = false" />
            @if($action === 'edit')
            <x-button
                label="Eliminar"
                icon="o-trash"
                class="btn-accent btn-outline rounded-3xl !uppercase"
                wire:click="delete"
                spinner="delete"
                wire:target="delete,submit"
                wire:loading.attr="disabled" />
            @endif
            <x-button
                label="Guardar"
                icon="o-check"
                class="btn-primary rounded-3xl !uppercase"
                wire:click="submit"
                spinner="submit"
                wire:target="delete,submit"
                wire:loading.attr="disabled" />
        </x-slot:actions>

        <div>
            <x-form wire:submit.prevent="submit" class="gap-5">
                <div class="space-y-5">
                    <x-input
                        label="Nombre"
                        class="rounded-3xl"
                        wire:model="form.name" />

                    <x-input
                        label="Apellidos"
                        class="rounded-3xl"
                        wire:model="form.lastname" />

                    <x-input
                        label="NIF / DNI"
                        class="rounded-3xl"
                        wire:model="form.nif_document" />

                    <x-input
                        label="Correo electrónico"
                        class="rounded-3xl"
                        wire:model="form.email" />

                    <x-input
                        label="Teléfono"
                        class="rounded-3xl"
                        wire:model="form.phone_number" />

                    <div class="grid md:grid-cols-3 gap-5">
                        <div class="col-span-1">
                            <x-input
                                label="Dirección"
                                class="rounded-3xl"
                                wire:model="form.address" />
                        </div>
                        <div class="col-span-1">
                            <x-input
                                label="Ciudad"
                                class="rounded-3xl"
                                wire:model="form.city_name" />
                        </div>
                        <div class="col-span-1">
                            <x-input
                                label="Código Postal"
                                class="rounded-3xl"
                                wire:model="form.postal_code" />
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-5">
                        <div class="col-span-1">
                            <x-input
                                label="Provincia"
                                class="rounded-3xl"
                                wire:model="form.province_name" />
                        </div>
                        <div class="col-span-1">
                            <x-input
                                label="País"
                                class="rounded-3xl"
                                wire:model="form.country_name" />
                        </div>
                    </div>
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-4">
                            <input
                                type="checkbox"
                                wire:model="form.allow_publish_images"
                                class="checkbox checkbox-primary" />
                            <span class="label-text">El cliente permite publicar sus imágenes</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-4">
                            <input
                                type="checkbox"
                                wire:model="form.allow_commercial_comms"
                                class="checkbox checkbox-primary" />
                            <span class="label-text">El cliente permite comunicaciones comerciales</span>
                        </label>
                    </div>

                    <x-editor
                        label="Notas / Observaciones"
                        class="rounded-3xl w-2/3 md:w-full"
                        wire:model="form.notes"
                        :config="$tinyMCE_settings" />
                </div>
            </x-form>
        </div>
    </x-drawer>
</div>