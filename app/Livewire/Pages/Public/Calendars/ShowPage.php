<?php

namespace App\Livewire\Pages\Public\Calendars;

use App\Models\User;
use App\Models\Client;
use Mary\Traits\Toast;
use App\Models\Service;
use Livewire\Component;
use App\Models\Calendar;
use App\Models\ServicePack;
use Livewire\Attributes\On;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\PaymentMethod;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Computed;
use App\Livewire\Forms\ClientForm;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Livewire\Forms\BookingForm;
use App\Models\CalendarAvailability;
use App\Notifications\Bookings\PhotographerBooked;

#[Layout('components.layouts.public')]
class ShowPage extends Component
{
    use Toast;

    #[Locked]
    public string $slug;

    public Calendar $calendar;
    public User $tenant;
    public ClientForm $clientForm;
    public BookingForm $bookingForm;

    public string $logo = '';
    public string $step = 'services';
    public string $prev_step = '';
    public bool $booked = false;
    public array $history = ['services'];

    public ?int $service = null;
    public ?int $service_pack = null;
    public string $payment_method = "1";
    public ?int $calendar_availability = null;
    public array $assistants = [];
    public array $questions = [];
    public string $payment_url = '';

    public function mount($slug)
    {
        $this->calendar = Calendar::query()
            ->with('tenant:id,name,lastname,email,transfer_details,stripe_pub,stripe_priv', 'tenant.media')
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        $this->tenant = $this->calendar->tenant;
        $this->logo = $this->tenant->getFirstMediaUrlCustom('logo') ?? '';
    }

    public function back()
    {
        array_pop($this->history);
        $this->step = $this->history[count($this->history) - 1];

        if ($this->step === 'availability') {
            $this->dispatch('show-availability', servicePackId: $this->service_pack);
        }
    }

    public function selectService(int $id)
    {
        $this->service = $id;

        $this->step = 'packs';
        $this->history[] = 'packs';

        $this->questions = $this->calendar
            ->questions()
            ->where('is_active', true)
            ->whereHas('services', function ($query) {
                $query->where('id', $this->service);
            })
            ->orderBy('position')
            ->get()
            ->map(function ($question) {
                return [
                    'id' => $question->id,
                    'question' => $question->question,
                    'required' => $question->is_required,
                    'position' => $question->position,
                    'answer' => '',
                ];
            })
            ->values()
            ->toArray();
    }

    public function selectPack(int $id)
    {
        $this->service_pack = $id;

        $service = $this->services->firstWhere('id', $this->service);

        if ($service->with_reservation) {
            $this->step = 'availability';
            $this->history[] = 'availability';
            $this->dispatch('show-availability', servicePackId: $this->service_pack);
            return;
        }

        $this->step = 'client';
        $this->history[] = 'client';
    }

    public function submitClient()
    {
        // 1. validar datos cliente
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:255'],
        ];

        if ($this->calendar->require_nif_document) {
            $rules['nif_document'] = ['required', 'string', 'max:255'];
        }

        if ($this->calendar->require_address) {
            $rules['address'] = ['required', 'string', 'max:255'];
            $rules['postal_code'] = ['required', 'string', 'max:15'];
            $rules['country_name'] = ['required', 'string', 'max:255'];
            $rules['province_name'] = ['required', 'string', 'max:255'];
            $rules['city_name'] = ['required', 'string', 'max:255'];
        }

        $this->clientForm->validate($rules);

        // 2. validar datos asistentes
        // 3. validar datos preguntas
        $this->validate([
            'assistants' => ['sometimes', 'nullable', 'array'],
            'assistants.*.name' => ['required_with:assistants', 'string', 'max:255'],
            'assistants.*.birthday' => ['required_with:assistants', 'date'],
            'questions' => ['sometimes', 'nullable', 'array'],
            'questions.*.id' => ['required_with:questions', 'integer', 'exists:\App\Models\CalendarQuestion,id'],
            'questions.*.required' => ['required_with:questions', 'boolean'],
            'questions.*.answer' => ['sometimes', 'required_if_accepted:questions.*.required', 'string'],
        ], [
            'assistants.*.name.required_with' => 'Campo requerido',
            'assistants.*.birthday.required_with' => 'Campo requerido',
            'questions.*.answer' => 'Respuesta requerida',
        ]);

        $this->step = 'booking';
        $this->history[] = 'booking';
    }

    public function confirm()
    {
        DB::transaction(function () {
            $service = $this->serviceSelected();
            $service_pack = $this->packSelected();

            // 1. chequear cliente
            $client = Client::query()
                ->where('email', $this->clientForm->email)
                ->where('tenant_id', $this->tenant->id)
                ->first();

            if (is_null($client)) {
                $this->clientForm->tenant_id = $this->tenant->id;
                $client = $this->clientForm->store();

            } else {
                $this->clientForm->set($client);
                $this->clientForm->update();
            }

            // 2. crear reserva
            $this->bookingForm->calendar_id = $this->calendar->id;
            $this->bookingForm->status = BookingStatus::PENDING;
            $this->bookingForm->client_id = $client->id;
            $this->bookingForm->service_pack_id = $this->service_pack;
            $this->bookingForm->calendar_availability_id = $this->calendar_availability;
            $booking = $this->bookingForm->store();

            // 2. crear pago
            $stripe_payment = null;
            if ($this->payment_method == PaymentMethod::STRIPE) {
                \Stripe\Stripe::setApiKey($this->tenant->stripe_priv);

                $stripe_payment = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => $service->name . ' - '. $service_pack->name,
                            ],
                            'unit_amount' => $service_pack->reservation_price * 100,
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => config('app.landing_url'),
                    'cancel_url' => config('app.landing_url'),
                    'metadata' => [
                        'booking_id' => $booking->id,
                    ],
                ]);

                $this->payment_url = $stripe_payment->url;
            }

            $booking->payment()->create([
                'payment_method_id' => (int) $this->payment_method,
                'status' => PaymentStatus::PENDING,
                'amount' => $service_pack->reservation_price,
                'external_id' => $stripe_payment?->id,
                'external_url' => $stripe_payment?->url,
            ]);

            // 3. crear respuestas
            if (!empty($this->questions)) {
                $questions = array_map(fn ($question) => [
                    'calendar_question_id' => $question['id'],
                    'answer' => $question['answer'],
                ], $this->questions);
                $booking->answers()->createMany($questions);
            }

            // 4. vincular asistentes
            if (!empty($this->assistants)) {
                $booking->assistants()->createMany($this->assistants);
            }

            // 5. enviar notificación
            $this->tenant->notify(new PhotographerBooked($booking));
        });

        $this->booked = true;

        if (!empty($this->payment_url)) {
            $this->dispatch('redirect-to-payment', url: $this->payment_url);
        }
    }

    /**
     * assistants
     */
    public function addAssistant()
    {
        $this->assistants[] = [
            'name' => '',
            'birthday' => null,
        ];
    }

    public function removeAssistant(int $index)
    {
        unset($this->assistants[$index]);
        $this->assistants = array_values($this->assistants);
    }

    #[Computed]
    public function steps(): array
    {
        return [
            [
                'name' => 'Servicios',
                'active' => in_array($this->step, ['services', 'packs', 'availability', 'client', 'booking']),
            ],
            [
                'name' => 'Paquetes',
                'active' => in_array($this->step, ['packs', 'availability', 'client', 'booking']),
            ],
            [
                'name' => 'Fecha y Hora',
                'active' => in_array($this->step, ['availability', 'client', 'booking']),
            ],
            [
                'name' => 'Datos',
                'active' => in_array($this->step, ['client', 'booking']),
            ],
            [
                'name' => 'Reserva',
                'active' => in_array($this->step, ['booking']),
            ],
        ];
    }

    #[Computed]
    public function services(): Collection
    {
        if (is_null($this->calendar)) {
            return collect();
        }

        $services = $this->calendar->services()
            ->where('is_active', true)
            ->get();

        return $services;
    }

    #[Computed]
    public function packs(): Collection
    {
        if (is_null($this->service)) {
            return collect();
        }

        $packs = ServicePack::query()
            ->where('is_active', true)
            ->where('service_id', $this->service)
            ->get();

        return $packs;
    }

    #[Computed]
    public function serviceSelected(): ?Service
    {
        if (is_null($this->service)) {
            return null;
        }

        return $this->services->firstWhere('id', $this->service);
    }

    #[Computed]
    public function packSelected(): ?ServicePack
    {
        if (is_null($this->service_pack)) {
            return null;
        }

        return $this->packs->firstWhere('id', $this->service_pack);
    }

    #[Computed]
    public function paymentMethods(): Collection
    {
        $has_stripe = $this->tenant->has_stripe;

        $payment_methods = PaymentMethod::query()
            ->when(!$has_stripe, fn ($query) => $query->where('id', '!=', PaymentMethod::STRIPE))
            ->get();

        return $payment_methods;
    }

    #[Computed]
    public function paymentMethod(): ?PaymentMethod
    {
        return $this->paymentMethods->firstWhere('id', $this->payment_method);
    }

    #[Computed]
    public function availability(): ?CalendarAvailability
    {
        if (is_null($this->calendar_availability)) {
            return null;
        }

        return CalendarAvailability::query()
            ->where('id', $this->calendar_availability)
            ->first();
    }

    #[On('select-availability')]
    public function selectAvailability(int $id)
    {
        $this->calendar_availability = $id;

        $this->step = 'client';
        $this->history[] = 'client';
    }

    public function render()
    {
        return view('livewire.pages.public.calendars.show-page');
    }
}
