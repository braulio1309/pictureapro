@assets
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/list@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales-all.global.js"></script>

    @vite(['resources/css/fullcalendar.css'])
@endassets

<div class="bg-white min-h-screen max-w-screen-lg mx-auto shadow-lg">
    <div class="py-5 mb-12">
        @if(!empty($logo))
            <img src="{{ $logo }}" alt="Logo" class="h-32 mx-auto">
        @else
            <div class="text-5xl text-primary text-center">
                <span class="font-bold">Picturea</span>PRO
            </div>
        @endif
    </div>

    <div class="grid grid-cols-2 px-12 gap-12 items-center">
        @if($this->calendar->cover)
            <div class="col-span-full md:col-span-1">
                <img
                    src="{{ $this->calendar->cover }}"
                    alt="Portada {{ $this->calendar->name }}"
                    class="w-full md:w-auto md:h-64 mx-auto rounded-3xl"
                >
            </div>
        @endif
        <div class="col-span-full @if($this->calendar->cover) md:col-span-1 @endif text-center space-y-3">
            <h1 class="font-semibold text-3xl">{{ $this->calendar->name }}</h1>
            {!! $this->calendar->description !!}
        </div>
    </div>

    @if(!$booked)
        <div class="px-0 py-10">
            <ul class="steps w-full">
                @foreach($this->steps as $tab)
                    <li class="step @if($tab['active']) step-primary @endif">{{ $tab['name'] }}</li>
                @endforeach
            </ul>

            <div class="px-8 md:px-16 mt-10">
                @if($step === 'services')
                    <div class="grid grid-cols-6 gap-8">
                        @foreach($this->services as $service)
                            <div class="col-span-full md:col-span-3 lg:col-span-2">
                                <x-card :title="$service->name" class="shadow-lg !rounded-3xl">
                                    <div class="prose">
                                        {!! $service->description !!}
                                    </div>
                                
                                    <x-slot:figure>
                                        <img src="{{ $service->cover ?? asset('images/placeholder.webp') }}" />
                                    </x-slot:figure>
                                    <x-slot:actions>
                                        <x-button
                                            label="Seleccionar"
                                            class="btn-primary rounded-3xl"
                                            @click="$wire.selectService({{ $service->id }})"
                                        />
                                    </x-slot:actions>
                                </x-card>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($step === 'packs')
                    <div class="grid grid-cols-6 gap-8">
                        @foreach($this->packs as $pack)
                            <div class="col-span-full md:col-span-3 lg:col-span-2">
                                <x-card title="{{ $pack->name }} - €{{ $pack->price }}" class="shadow-lg !rounded-3xl">
                                    <div class="prose">
                                        {!! $pack->description !!}
                                    </div>
                                
                                    <x-slot:figure>
                                        <img src="{{ $pack->cover ?? asset('images/placeholder.webp') }}" />
                                    </x-slot:figure>
                                    <x-slot:actions>
                                        <x-button
                                            label="Seleccionar"
                                            class="btn-primary rounded-3xl"
                                            @click="$wire.selectPack({{ $pack->id }})"
                                        />
                                    </x-slot:actions>
                                </x-card>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($step === 'availability')
                    <div id="calendar" class="mt-5 h-screen" wire:ignore></div>
                @endif

                @if($step === 'client')
                    <div class="space-y-5">
                        <div>
                            <h2 class="text-3xl font-medium">Datos personales</h2>

                            <x-form wire:submit.prevent="submit" class="gap-5 mt-5">
                                <div class="grid md:grid-cols-2 gap-5">
                                    <div class="md:col-span-1">
                                        <x-input
                                            label="Nombre"
                                            class="rounded-3xl"
                                            wire:model="clientForm.name"
                                            required
                                        />
                                    </div>

                                    <div class="md:col-span-1">
                                        <x-input
                                            label="Apellidos"
                                            class="rounded-3xl"
                                            wire:model="clientForm.lastname"
                                            required
                                        />
                                    </div>

                                    @if($calendar->require_nif_document)
                                        <div class="col-span-full">
                                            <x-input
                                                label="NIF / DNI"
                                                class="rounded-3xl"
                                                wire:model="clientForm.nif_document"
                                                required
                                            />
                                        </div>
                                    @endif

                                    <div class="col-span-full">
                                        <x-input
                                            label="Correo electrónico"
                                            class="rounded-3xl"
                                            wire:model="clientForm.email"
                                            required
                                        />
                                    </div>

                                    <div class="col-span-full">
                                        <div class="col-span-1">
                                            <x-input
                                                label="Teléfono"
                                                class="rounded-3xl"
                                                wire:model="clientForm.phone_number"
                                                required
                                            />
                                        </div>
                                    </div>

                                    @if($calendar->require_address)
                                        <div class="col-span-full grid md:grid-cols-3 gap-5">
                                            <div class="col-span-1">
                                                <x-input
                                                    label="Dirección"
                                                    class="rounded-3xl"
                                                    wire:model="clientForm.address"
                                                    required
                                                />
                                            </div>
                                            <div class="col-span-1">
                                                <x-input
                                                    label="Ciudad"
                                                    class="rounded-3xl"
                                                    wire:model="clientForm.city_name"
                                                    required
                                                />
                                            </div>
                                            <div class="col-span-1">
                                                <x-input
                                                    label="Código Postal"
                                                    class="rounded-3xl"
                                                    wire:model="clientForm.postal_code"
                                                    required
                                                />
                                            </div>
                                        </div>
                                
                                        <div class="col-span-full grid md:grid-cols-2 gap-5">
                                            <div class="col-span-1">
                                                <x-input
                                                    label="Provincia"
                                                    class="rounded-3xl"
                                                    wire:model="clientForm.province_name"
                                                    required
                                                />
                                            </div>
                                            <div class="col-span-1">
                                                <x-input
                                                    label="País"
                                                    class="rounded-3xl"
                                                    wire:model="clientForm.country_name"
                                                    required
                                                />
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </x-form>
                        </div>

                        @if($calendar->require_assistants)
                            <div class="space-y-3">
                                <h2 class="text-3xl font-medium">Asistentes</h2>
            
                                <x-button
                                    label="Añadir"
                                    icon="s-plus"
                                    class="btn-primary btn-sm btn-outline rounded-3xl !uppercase"
                                    wire:click="addAssistant"
                                />
            
                                <div class="grid grid-cols-2 gap-5">
                                    @foreach($assistants as $assistant)
                                        <div
                                            wire:key="assistant-{{ $loop->index }}"
                                            class="p-5 shadow-lg rounded-3xl space-y-5 col-span-full md:col-span-1"
                                        >
                                            <x-input
                                                label="Nombre"
                                                class="rounded-3xl max-w-full"
                                                wire:model="assistants.{{ $loop->index }}.name"
                                            />
            
                                            <x-input
                                                label="Fecha nacimiento"
                                                type="date"
                                                class="rounded-3xl max-w-full"
                                                wire:model="assistants.{{ $loop->index }}.birthday"
                                            />
            
                                            <x-button
                                                icon="s-trash"
                                                class="btn-accent btn-outline btn-circle btn-sm"
                                                wire:click="removeAssistant({{ $loop->index }})"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(!empty($questions))
                            <div class="space-y-3">
                                <h2 class="text-3xl font-medium">Preguntas</h2>

                                @foreach($questions as $question)
                                    <div class="space-y-2" wire:key="question-{{ $question['id'] }}">
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
                        @endif
                    </div>

                @endif

                @if($step === 'booking')
                    <div class="space-y-5">
                        <div>
                            <h2 class="text-3xl font-medium">Resúmen reserva</h2>

                            <div class="mt-3">
                                <x-list-item :item="[]" no-hover>
                                    <x-slot:avatar>
                                        <x-icon name="o-list-bullet" />
                                    </x-slot:avatar>
                                    <x-slot:value>
                                        {{ $this->serviceSelected->name }}
                                    </x-slot:value>
                                    <x-slot:sub-value>
                                        Servicio
                                    </x-slot:sub-value>
                                </x-list-item>

                                <x-list-item :item="[]" no-hover>
                                    <x-slot:avatar>
                                        <x-icon name="o-shopping-cart" />
                                    </x-slot:avatar>
                                    <x-slot:value>
                                        {{ $this->packSelected->name }}
                                    </x-slot:value>
                                    <x-slot:sub-value>
                                        Paquete
                                    </x-slot:sub-value>
                                </x-list-item>

                                @if($this->availability)
                                    <x-list-item :item="[]" no-hover>
                                        <x-slot:avatar>
                                            <x-icon name="o-calendar" />
                                        </x-slot:avatar>
                                        <x-slot:value>
                                            {{ $this->availability->start_full.' hs.' }}
                                        </x-slot:value>
                                        <x-slot:sub-value>
                                            Fecha y Hora
                                        </x-slot:sub-value>
                                    </x-list-item>
                                @endif

                                <x-list-item :item="[]" no-hover>
                                    <x-slot:avatar>
                                        <x-icon name="o-currency-euro" />
                                    </x-slot:avatar>
                                    <x-slot:value>
                                        €{{ $this->packSelected->price }}
                                    </x-slot:value>
                                    <x-slot:sub-value>
                                        Precio
                                    </x-slot:sub-value>
                                </x-list-item>

                                <x-list-item :item="[]" no-separator no-hover>
                                    <x-slot:avatar>
                                        <x-icon name="o-banknotes" />
                                    </x-slot:avatar>
                                    <x-slot:value>
                                        €{{ $this->packSelected->reservation_price }}
                                    </x-slot:value>
                                    <x-slot:sub-value>
                                        Valor Reserva
                                    </x-slot:sub-value>
                                </x-list-item>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-3xl font-medium">Datos</h2>

                            <div class="mt-3">
                                <x-list-item :item="[]" no-hover>
                                    <x-slot:avatar>
                                        <x-icon name="o-user" />
                                    </x-slot:avatar>
                                    <x-slot:value>
                                        {{ $clientForm->name . ' '. $clientForm->lastname  }}
                                    </x-slot:value>
                                    <x-slot:sub-value>
                                        Nombre
                                    </x-slot:sub-value>
                                </x-list-item>

                                <x-list-item :item="[]" no-hover>
                                    <x-slot:avatar>
                                        <x-icon name="o-envelope" />
                                    </x-slot:avatar>
                                    <x-slot:value>
                                        {{ $clientForm->email  }}
                                    </x-slot:value>
                                    <x-slot:sub-value>
                                        Correo electrónico
                                    </x-slot:sub-value>
                                </x-list-item>

                                @if($this->calendar->require_nif_document)
                                    <x-list-item :item="[]" no-hover>
                                        <x-slot:avatar>
                                            <x-icon name="o-identification" />
                                        </x-slot:avatar>
                                        <x-slot:value>
                                            {{ $clientForm->nif_document  }}
                                        </x-slot:value>
                                        <x-slot:sub-value>
                                            NIF / DNI
                                        </x-slot:sub-value>
                                    </x-list-item>
                                @endif

                                @if($this->calendar->require_address)
                                    <x-list-item :item="[]" no-hover>
                                        <x-slot:avatar>
                                            <x-icon name="o-map" />
                                        </x-slot:avatar>
                                        <x-slot:value>
                                            {{ $clientForm->address  }}
                                        </x-slot:value>
                                        <x-slot:sub-value>
                                            Dirección
                                        </x-slot:sub-value>
                                    </x-list-item>
                                @endif

                                <x-list-item :item="[]" no-separator no-hover>
                                    <x-slot:avatar>
                                        <x-icon name="o-phone" />
                                    </x-slot:avatar>
                                    <x-slot:value>
                                        {{ $clientForm->phone_number  }}
                                    </x-slot:value>
                                    <x-slot:sub-value>
                                        Teléfono
                                    </x-slot:sub-value>
                                </x-list-item>
                            </div>
                        </div>

                        @if(!empty($assistants))
                            <div>
                                <h2 class="text-3xl font-medium">Asistentes</h2>

                                <div class="mt-3">
                                    @foreach($assistants as $assistant)
                                        <x-list-item :item="[]" no-hover>
                                            <x-slot:avatar>
                                                <x-icon name="o-user" />
                                            </x-slot:avatar>
                                            <x-slot:value>
                                                {{ $assistant['name']  }}
                                            </x-slot:value>
                                            <x-slot:sub-value>
                                                {{ date('d/m/Y', strtotime($assistant['birthday'])) }}
                                            </x-slot:sub-value>
                                        </x-list-item>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div>
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <h2 class="text-3xl font-medium mb-3">Formas de pago</h2>

                            @if($this->paymentMethods->isNotEmpty())
                                <x-tabs
                                    wire:model="payment_method"
                                    active-class="btn !btn-accent !text-white rounded-3xl space-x-5"
                                    label-class="btn btn-secondary rounded-3xl text-white text-md font-bold"
                                    label-div-class="bg-white space-x-1.5"
                                >
                                    @foreach($this->paymentMethods as $method)
                                        <x-tab :name="$method->id" :label="$method->name"></x-tab>
                                    @endforeach
                                </x-tabs>
                            @endif
                        </div>
                    </div>
                @endif

                @if($step !== 'services')
                    <div class="mt-5 flex flex-row justify-end space-x-1">
                        <x-button
                            label="Volver"
                            class="btn-primary btn-outline rounded-3xl"
                            @click="$wire.back"
                        />

                        @if($step === 'client')
                            <x-button
                                label="Siguiente"
                                class="btn-primary rounded-3xl"
                                @click="$wire.submitClient"
                            />
                        @endif
                        @if($step === 'booking')
                            <x-button
                                label="Confirmar"
                                class="btn-primary rounded-3xl"
                                @click="$wire.confirm"
                            />
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="px-20 py-10">
            <div class="text-primary text-center">
                <x-icon name="s-check-circle" class="h-40" />
                <h2 class="text-4xl font-bold">Reserva realizada</h2>
            </div>

            <div class="px-8 md:px-16 mt-10 pb-10 text-center">
                <h2 class="text-3xl font-medium mb-3">Información de pago</h2>

                @if($this->paymentMethod && $this->paymentMethod->id === \App\Models\PaymentMethod::BANK_TRANSFER)
                    <div class="border-dashed border border-primary rounded-3xl p-5">
                        {!! $this->tenant->transfer_details !!}
                    </div>
                @endif

                @if($this->paymentMethod && $this->paymentMethod->id === \App\Models\PaymentMethod::STRIPE)
                    <div class="space-y-3">
                        <x-button
                            label="Ir a Pagar"
                            class="btn-accent rounded-3xl btn-lg"
                            :link="$payment_url"
                            external
                        />

                        <p class="text-slate-500">Serás redirigido automaticamente o puedes hacer click en el botón</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

@script
<script>
    $wire.on('show-availability', function(event) {
        const servicePackId = event.servicePackId

        setTimeout(() => {
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
                    const filters = {
                        pack: servicePackId,
                    };
        
                    axios.get('/api/public/calendars/' + calendarId + '/availabilities', {
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

                    if (!info.event.extendedProps.available) {
                        alert('No disponible');
                        return;
                    }
        
                    const calendar = info.event.extendedProps.calendar_id;
                    const availability = info.event.extendedProps.calendar_availability_id;

                    $wire.dispatch('select-availability', { id: availability });
                }
            });
        
            calendar.render();
        }, 100);
    });

    $wire.on('redirect-to-payment', function(event) {
        setTimeout(() => {
            window.location.href = event.url;
        }, 5000);
    });
</script>
@endscript