<div>
    <x-drawer
        id="booking-drawer"
        wire:model="open"
        class="w-full lg:w-2/3"
        right
        title="{{ $action === 'create' ? 'Nueva reserva' : 'Editar reserva #'.$this->form->booking->code }}"
        separator
    >
        <x-slot:actions>
            <div class="flex items-center gap-2">
                <x-loading class="text-primary" wire:loading />
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
                        wire:target="delete,submit"
                        wire:loading.attr="disabled"
                    />
                @endif
                <x-button
                    label="Guardar"
                    icon="o-check"
                    class="btn-primary rounded-3xl !uppercase"
                    wire:click="submit"
                    wire:target="delete,submit"
                    wire:loading.attr="disabled"
                />
            </div>
        </x-slot:actions>

        <div>
            @if($action === 'edit')
                <div class="absolute z-10 right-10">
                    <x-dropdown>
                        <x-slot:trigger>
                            <x-button icon="o-ellipsis-vertical" class="btn-primary btn-outline btn-circle" />
                        </x-slot:trigger>
                    
                        @if($this->paymentStatus === \App\Enums\PaymentStatus::PENDING)
                            <x-menu-item
                                title="Confirmar pago"
                                icon="o-currency-euro"
                                wire:click="confirmPayment"
                            />
                        @endif
                        @if($this->paymentStatus === \App\Enums\PaymentStatus::COMPLETED)
                            <x-menu-item
                                title="Cancelar pago"
                                icon="o-currency-euro"
                                wire:click="cancelPayment"
                            />
                        @endif
                        @if($this->bookingStatus === \App\Enums\BookingStatus::CANCELLED)
                            <x-menu-item
                                title="Marcar como pendiente"
                                icon="o-clock"
                                wire:click="pending"
                            />
                        @endif
                        @if($this->bookingStatus !== \App\Enums\BookingStatus::CANCELLED)
                            <x-menu-item
                                title="Cancelar sesión"
                                icon="s-x-mark"
                                wire:click="cancel"
                            />
                        @endif
                    </x-dropdown>
                </div>
            @endif

            <x-tabs
                wire:model="tab_selected"
                active-class="btn !btn-accent !text-white rounded-3xl space-x-5"
                label-class="btn btn-secondary rounded-3xl text-white text-md font-bold"
                label-div-class="bg-white space-x-1.5"
            >
                <x-tab name="general" label="General" icon="s-cog-6-tooth">
                    <x-form wire:submit.prevent="submit">
                        <div class="space-y-5">
                            <div class="flex flex-row justify-between items-center gap-2">
                                <div class="w-full">
                                    <x-choices
                                        label="Cliente"
                                        class="rounded-3xl"
                                        wire:model.live="form.client_id"
                                        :options="$clients"
                                        option-sub-label="sublabel"
                                        placeholder="Buscar..."
                                        no-result-text="No se encontraron resultados"
                                        searchable
                                        search-function="searchClients"
                                        debounce="300ms"
                                        single
                                        required
                                    />
                                </div>

                                <div class="text-center">
                                    <div class="pt-0 label label-text font-semibold text-white">A</div>
                                    <x-button
                                        icon="s-plus" 
                                        class="btn-circle btn-primary btn-outline"
                                        link="{{ route('dashboard.clients.index') }}"
                                    />
                                </div>
                            </div>

                            @if(!is_null($this->client))
                                <x-list-item :item="$this->client" no-separator no-hover class="rounded-3xl shadow-md">
                                    <x-slot:value>
                                        {{ $this->client->fullname }}
                                    </x-slot:value>
                                    <x-slot:sub-value>
                                        <p>{{ $this->client->email }}</p>
                                        @if(!is_null($this->client->phone_number))
                                            <p>{{ $this->client->phone_number }}</p>
                                        @endif
                                    </x-slot:sub-value>
                                </x-list-item>
                            @endif

                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-1">
                                    <x-choices-offline
                                        label="Servicio"
                                        class="rounded-3xl"
                                        wire:model.live.change="form.service_id"
                                        :options="$this->services"
                                        placeholder="Buscar..."
                                        noResultText="No se encontraron resultados"
                                        single
                                        searchable
                                        required
                                        wire:target="form.client_id,form.service_id"
                                        wire:loading.attr="disabled"
                                    />
                                </div>

                                @if(!is_null($this->form->service_id))
                                    <div class="col-span-1">
                                        <x-choices-offline
                                            label="Packs"
                                            class="rounded-3xl"
                                            wire:model.live.change="form.service_pack_id"
                                            :options="$this->packs"
                                            placeholder="Buscar..."
                                            noResultText="No se encontraron resultados"
                                            single
                                            searchable
                                            required
                                            wire:target="form.client_id,form.service_id,form.service_pack_id"
                                            wire:loading.attr="disabled"
                                        />
                                    </div>
                                @endif
                            </div>

                            @if(!is_null($this->pack))
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-1">
                                        <x-input
                                            label="Precio sesión"
                                            class="rounded-3xl"
                                            icon-right="o-currency-euro"
                                            :value="$this->pack->price"
                                            readonly
                                        />
                                    </div>

                                    <div class="col-span-1">
                                        <x-input
                                            label="Reserva"
                                            class="rounded-3xl text-"
                                            icon-right="o-currency-euro"
                                            :value="$this->pack->reservation_price"
                                            readonly
                                        />
                                    </div>
                                </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-1 space-y-4">
                                    <x-button
                                        icon="{{ $with_availability ? 'o-x-mark' : 'o-calendar' }}"
                                        class="{{ $with_availability ? 'btn-accent' : 'btn-secondary ' }} rounded-3xl text-white"
                                        @click="$dispatch('toggle-availability')"
                                        :disabled="$this->service && !$this->service->with_reservation"
                                    >
                                        {{ $with_availability ? 'Sin cita' : 'Asignar cita' }}
                                    </x-button>

                                    @if($with_availability)
                                        <x-datepicker
                                            label="Fecha"
                                            wire:model.live="availability_date"
                                            icon="o-calendar"
                                            class="rounded-3xl"
                                            :config="$this->availabilityConfig"
                                        />

                                        @if(!empty($this->availabilities))
                                            <x-radio
                                                label="Citas disponibles"
                                                :options="$this->availabilities"
                                                wire:model.live="form.calendar_availability_id" 
                                                class="rounded-3xl"
                                            />
                                        @endif
                                    @endif
                                </div>

                            </div>

                            @if($action === 'edit')
                                <div>
                                    <p class="font-medium">Información sobre el pago</p>
                                    <p>Método de pago: {{ $this->form->booking->payment->method->name }}</p>
                                    <p>{{ $this->form->booking->payment->status->getLabel() }} desde {{ $this->form->booking->payment->status_changed_at->format('d/m/Y H:i') }} hs.</p>
                                </div>
                            @endif
                        </div>
                    </x-form>
                </x-tab>

                <x-tab name="questions" label="Cuestionario" icon="o-queue-list">
                    <x-form wire:submit.prevent="submit" wire:key="{{ $questions_key }}">
                        <div class="mt-3 space-y-5">
                            @foreach($questions as $question)
                                <div class="space-y-2" wire:key="{{ $questions_key }}-question-{{ $question['id'] }}">
                                    <p class="font-semibold">{{ $question['question'] }}</p>

                                    <x-textarea
                                        wire:model="questions.{{ $loop->index }}.answer"
                                        class="rounded-3xl"
                                        placeholder="Respuesta.."
                                        rows="4"
                                        :required="$question['required']"
                                    />
                                </div>
                            @endforeach
                        </div>
                    </x-form>
                </x-tab>
            </x-tabs>
        </div>
    </x-drawer>
</div>
