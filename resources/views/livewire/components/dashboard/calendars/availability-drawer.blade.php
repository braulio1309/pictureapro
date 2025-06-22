<div>
    <x-drawer
        id="calendar-availability-drawer"
        wire:model="open"
        class="w-full lg:w-2/3"
        right
        title="Configurando cita"
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
                    wire:target="delete,submit"
                    wire:loading.attr="disabled"
                />
            @endif
            <x-button
                label="Guardar"
                icon="o-check"
                class="btn-primary rounded-3xl !uppercase"
                wire:click="submit"
                spinner="submit"
                wire:target="delete,submit"
                wire:loading.attr="disabled"
            />
        </x-slot:actions>

        <div>
            <x-form wire:submit.prevent="submit" class="gap-5">
                <div class="space-y-5">
                    <x-input
                        label="Fecha"
                        class="rounded-3xl"
                        wire:model="form.date"
                        type="date"
                        disabled
                    />

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-1">
                            <x-input
                                label="Desde"
                                class="rounded-3xl"
                                wire:model="form.start_time"
                                disabled
                            />
                        </div>

                        <div class="col-span-1">
                            <x-input
                                label="Hasta"
                                class="rounded-3xl"
                                wire:model="form.end_time"
                                disabled
                            />
                        </div>
                    </div>

                    <x-input
                        label="Capacidad"
                        class="rounded-3xl"
                        wire:model="form.capacity"
                    />

                    <x-choices
                        label="Servicios"
                        class="rounded-3xl"
                        wire:model="form.packs"
                        :options="$this->packs"
                    />

                    <x-button
                        icon="o-plus"
                        class="btn-secondary rounded-3xl"
                        @click="$wire.open = false; $dispatch('bookings:open-drawer', { calendar: {{ $form->calendar_id }}, availability: {{ $form->availability?->id }} })"
                    >
                        Crear reserva
                    </x-button>
                </div>
            </x-form>
        </div>
    </x-drawer>
</div>
