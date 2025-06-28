<div>
    <!-- HEADER -->
     
    <x-header title="Cliente" separator progress-indicator>
        <x-slot:actions>
            <x-button
                label="Volver"
                class="btn-outline rounded-3xl"
                icon="o-arrow-uturn-left"
                wire-navigate
                link="{{ route('dashboard.clients.index') }}"
            />
            <x-button
                label="Editar"
                class="btn-primary rounded-3xl"
                icon="s-pencil"
                @click="$dispatch('open-drawer', { action: 'edit', id: {{ $client->id }} })"
            />
        </x-slot:actions>
    </x-header>

    <div class="space-y-8">
        <x-card class="bg-white rounded-3xl shadow-xl">
            @foreach ($items as $item)
                <x-list-item
                    :item="$item"
                    value="label"
                    sub-value="value"
                />
            @endforeach
        </x-card>

        @if(!empty($client->notes))
            <x-card class="bg-white rounded-3xl shadow-xl">
                <h2 class="font-bold text-lg">Notas / Observaciones</h2>
                <div class="prose mt-3">
                    {!! $client->notes !!}
                </div>
            </x-card>
        @endif

        <div class="col-span-full">
            @if($this->bookings->isNotEmpty())
                <div class="col-span-full mt-5">
                    <h2 class="text-2xl font-medium">Reservas</h2>
                    @foreach($this->bookings as $booking)
                        <x-list-item :item="$booking" no-separator no-hover>
                            <x-slot:avatar>
                                <x-badge value="{{ $booking->status->getLabel() }}" class="badge-{{ $booking->status->getColor() }}" />
                            </x-slot:avatar>
                            <x-slot:value>
                                #{{ $booking->code }} - {{ $booking->client->fullname }}
                            </x-slot:value>
                            <x-slot:sub-value>
                                {{ $booking->pack->name }} - {{ $booking->pack->service->name }} - {{ $booking->created_at->format('d/m/Y') }}
                            </x-slot:sub-value>
                            <x-slot:actions>
                                <x-button
                                    icon="o-eye"
                                    class="btn-circle"
                                    @click="$wire.openBooking({{ $booking->calendar_id }}, {{ $booking->id }})"
                                />
                            </x-slot:actions>
                        </x-list-item>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <livewire:components.dashboard.clients.drawer />
    <livewire:components.dashboard.bookings.drawer />
</div>
