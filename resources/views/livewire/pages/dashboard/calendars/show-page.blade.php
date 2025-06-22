@assets
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/list@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales-all.global.js"></script>

    @vite(['resources/css/fullcalendar.css'])
@endassets

<div>
    <x-header :title="$calendar->name" separator progress-indicator>
        <x-slot:actions>
            <x-button
                label="Volver"
                class="btn-outline rounded-3xl"
                icon="o-arrow-uturn-left"
                link="{{ route('dashboard.calendars.index') }}"
            />
            <x-button
                label="Ver sitio"
                class="btn-accent text-white rounded-3xl"
                icon="o-link"
                :link="route('public.calendars.show', $calendar->slug)"
                :disabled="!$calendar->is_active"
                external
            />
            <x-button
                label="Configurar"
                class="btn-primary rounded-3xl"
                icon="o-cog-6-tooth"
                wire:click="openDrawer"
            />
        </x-slot:actions>
    </x-header>

    <div class="space-y-8 pb-32">
        <div class="grid md:grid-cols-5 gap-8">
            <div class="col-span-full md:col-span-2">
                <img src="{{ $calendar->cover ?? asset('images/placeholder.webp') }}" alt="{{ $calendar->name }}" class="w-full rounded-3xl mx-auto">
            </div>
            <div class="col-span-full md:col-span-3 flex flex-col space-y-4">
                <x-stat
                    title="Total"
                    :value="$this->stats['total']"
                    class="shadow-md"
                />

                <x-stat
                    title="Reservadas"
                    :value="$this->stats['confirmed']"
                    class="shadow-md"
                />

                <x-stat
                    title="En espera de pago"
                    :value="$this->stats['pending']"
                    class="shadow-md"
                />

                <x-stat
                    title="Libres"
                    :value="$this->stats['available']"
                    class="shadow-md" 
                />
            </div>
        </div>

        <div class="col-span-full flex flex-col space-y-3 md:space-y-0 md:flex-row md:justify-between">
            <div class="space-x-1 flex flex-row">
                @foreach ($this->bookingStatuses as $status)
                    <x-button
                        :label="$status['name']"
                        class="{{ $status['active'] ? 'btn-accent' : 'btn-secondary' }} text-white rounded-3xl"
                        :icon="$status['icon']"
                        @click="$dispatch('bookings:change-status', { status: '{{ $status['value'] }}' })"
                    />
                @endforeach
            </div>

            <div class="space-x-1">
                @foreach ($this->calendarViews as $view)
                    <x-button
                        :label="$view['name']"
                        class="{{ $view['active'] ? 'btn-accent' : 'btn-secondary' }} text-white rounded-3xl"
                        :icon="$view['icon']"
                        @click="$dispatch('calendars:change-view', { view: '{{ $view['value'] }}' })"
                    />
                @endforeach
            </div>
        </div>

        <div class="col-span-full flex flex-row items-center gap-5 justify-end">
            <div>
                <x-toggle
                    label="Ocultar pasadas"
                    wire:model.live.change="filters.from_now"
                    class="toggle-sm"
                    @change="$dispatch"
                />
            </div>

            <div>
                <x-datepicker
                    icon="o-calendar"
                    placeholder="Desde"
                    wire:model.live.change="filters.start_date"
                    class="rounded-3xl input-sm w-36"
                    :config="$datepicker_config"
                />
            </div>

            <div>
                <x-datepicker
                    icon="o-calendar"
                    placeholder="Hasta"
                    wire:model.live.change="filters.end_date"
                    class="rounded-3xl input-sm w-36"
                    :config="$datepicker_config"
                />
            </div>
        </div>

        <div class="col-span-full">
            @if($calendar_view === 'listWeek')
                @if($this->noAvailabilityBookings->isNotEmpty())
                    <div class="col-span-full mt-5">
                        <h2 class="text-2xl font-medium">Reservas sin fecha</h2>
                        @foreach($this->noAvailabilityBookings as $booking)
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
                                        @click="$wire.openOffBooking({{ $booking->id }})"
                                    />
                                </x-slot:actions>
                            </x-list-item>
                        @endforeach
                    </div>
                @endif

                @foreach($this->withAvailabilityBookings as $key => $bookings)
                    <div class="col-span-full mt-5">
                        <h2 class="text-2xl font-medium">{{ $key }}</h2>
                        @foreach($bookings as $booking)
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
                                        @click="$wire.openOffBooking({{ $booking->id }})"
                                    />
                                </x-slot:actions>
                            </x-list-item>
                        @endforeach
                    </div>
                @endforeach
            @endif

            <div class="@if($calendar_view === 'listWeek') hidden @endif">
                <div id="calendar" class="mt-5 h-screen" wire:ignore></div>
            </div>
        </div>
    </div>

    <div class="fixed bottom-4 right-4 z-50">
        <x-button icon="s-plus" class="btn-circle btn-lg btn-primary" @click="$wire.openOffBooking" />
    </div>

    <livewire:components.dashboard.calendars.drawer />
    <livewire:components.dashboard.calendars.availability-drawer />
    <livewire:components.dashboard.bookings.drawer />
</div>

@script
<script>
    const calendarId = @json($this->calendar->id);
    const calendarEl = document.getElementById('calendar');

    if (calendarEl === null) {
        return;
    }

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: false,
        locale: 'es',
        timeZone: 'Europe/Madrid',
        headerToolbar: {
            start: 'prev',
            center: 'title',
            end: 'next'
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        },
        firstDay: 1,
        events: function(fetchInfo, successCallback, failureCallback) {
            const filters = $wire.filters;
            filters.from_now = filters.from_now ? 1 : 0;

            axios.get('/api/calendars/' + calendarId + '/availabilities', {
                params: { filters: filters }
            })
                .then(response => successCallback(response.data))
                .catch(error => failureCallback(error));
        },
        eventClassNames: function(arg) {
            return [ arg.event.extendedProps.status ]
        },
        eventContent: function(arg) {
            return arg.event.title
        },
        eventClick: function(info) {
            info.jsEvent.preventDefault();

            const calendar = info.event.extendedProps.calendar_id;
            const booking = info.event.extendedProps.booking_id;
            const availability = info.event.extendedProps.calendar_availability_id;

            if (booking) {
                $wire.dispatch('bookings:open-drawer', { action: 'edit', calendar: calendar, id: booking });
            } else {
                $wire.dispatch('calendars:open-availability', { action: 'edit', id: availability });
            }
        }
    });

    calendar.render();

    $wire.on('calendars:availability-updated', (event) => {
        calendar.refetchEvents();
    });

    $wire.on('bookings:updated', (event) => {
        calendar.refetchEvents();
    });

    $wire.on('calendars:filters-updated', (event) => {
        console.log('aca');
        calendar.refetchEvents();
    });

    $wire.on('calendars:change-calendar-view', (event) => {
        if (event.view === 'listWeek') {
            if (calendar.isRendered) {
                // calendar.setOption('height', 0);
                calendar.destroy();
            }
            return;
        }

        if (!calendar.isRendered) {
            calendar.render()
            // calendar.setOption('height', '100vh');
        }

        setTimeout(() => {
            calendar.changeView(event.view);
        }, 100);
    });
</script>
@endscript