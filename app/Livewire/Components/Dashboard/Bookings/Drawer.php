<?php

namespace App\Livewire\Components\Dashboard\Bookings;

use App\Models\Client;
use Mary\Traits\Toast;
use App\Models\Booking;
use Livewire\Component;
use App\Models\Calendar;
use Livewire\Attributes\On;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\PaymentMethod;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Livewire\Forms\BookingForm;
use App\Models\CalendarAvailability;
use Illuminate\Support\Facades\Auth;
use App\Notifications\Bookings\ClientConfirmed;

class Drawer extends Component
{
    use Toast;

    public BookingForm $form;
    public ?int $calendar_id = null;
    public bool $open = false;
    public string $type = 'no-availability';
    public string $action = 'create';
    public string $tab_selected = 'general';

    public ?Calendar $calendar = null;
    // public ?Collection $services = null;
    public array $questions = [];
    public string $questions_key = '0000';
    public Collection $clients;

    public ?CalendarAvailability $availability = null;
    public bool $with_availability = false;
    public string $availability_date = '';

    public function mount()
    {
        $this->searchClients();
    }

    public function resetDrawer()
    {
        $this->tab_selected = 'general';
        $this->resetValidation();
        $this->form->reset();

        $this->questions = [];
        $this->questions_key = uniqid('booking-questions-');

        $this->availability = null;
        $this->with_availability = false;
        $this->availability_date = '';
    }

    #[On('bookings:open-drawer')]
    public function open(
        string $type = 'no-availability',
        ?int $calendar = null,
        string $action = 'create',
        ?string $id = null,
        ?int $availability = null
    ) {
        $this->type = $type;
        $this->action = $action;
        $this->resetDrawer();

        $this->calendar_id = $calendar;
        $this->form->calendar_id = $calendar;
        $this->loadCalendarData();

        if ($this->action === 'edit') {
            $booking = Booking::query()
                ->with(['availability', 'answers', 'pack', 'payment', 'payment.method'])
                ->findOrFail($id);

            if (!is_null($booking->calendar_availability_id)) {
                $this->with_availability = true;
                $this->availability_date = $booking->availability->date->format('Y-m-d');
                unset($this->availabilities);
            }

            $this->form->set($booking);
            $this->loadServiceQuestions();
        }

        $this->open = true;
        unset($this->availabilityConfig);

        if (!is_null($availability)) {
            $this->availability = CalendarAvailability::with('packs:id,service_id')->find($availability);
            $this->with_availability = true;
            $this->form->calendar_availability_id = $availability;
            $this->availability_date = $this->availability->date->format('Y-m-d');
            unset($this->services);
        }
    }

    #[On('toggle-availability')]
    public function toggleAvailability()
    {
        $this->with_availability = !$this->with_availability;

        $this->form->calendar_availability_id = null;
        $this->availability_date = '';
        unset($this->availabilities);
    }

    /**
     * computeds
     */
    #[Computed]
    public function client()
    {
        if (is_null($this->form->client_id)) {
            return null;
        }

        $client = Client::query()
            ->select('id', 'name', 'lastname', 'email', 'phone_number')
            ->where('tenant_id', Auth::id())
            ->findOrFail($this->form->client_id);

        return $client;
    }

    #[Computed]
    public function services(): Collection
    {
        if (is_null($this->calendar)) {
            return collect();
        }

        $services = $this->calendar->services;

        if (!is_null($this->form->calendar_availability_id)) {
            $availability = array_values(array_filter($this->availabilities, fn($availability) => $availability['id'] === $this->form->calendar_availability_id))[0];

            if (!empty($availability['services'])) {
                $services = $services->filter(fn($service) => in_array($service->id, $availability['services']));
            
                $check = $services->firstWhere('id', $this->form->service_id);
                if (is_null($check)) {
                    $this->form->service_id = null;
                    $this->form->service_pack_id = null;
                    unset($this->packs);
                }

                return $services;
            }
        }

        if ($this->with_availability) {
            $services = $services->filter(fn($service) => $service->with_reservation);
        }

        if (!is_null($this->availability) && $this->availability->packs->isNotEmpty()) {
            $services = $services->filter(fn($service) => $this->availability->packs->contains('service_id', $service->id));
        }

        return $services->values();
    }

    #[Computed]
    public function service()
    {
        if (is_null($this->form->service_id)) {
            return null;
        }

        return $this->services->firstWhere('id', $this->form->service_id);
    }

    #[Computed]
    public function packs(): Collection
    {
        if (is_null($this->form->service_id)) {
            return collect();
        }

        $service = $this->services->firstWhere('id', $this->form->service_id);
        $packs = $service->packs;

        return $packs;
    }

    #[Computed]
    public function pack()
    {
        if (is_null($this->form->service_pack_id)) {
            return null;
        }

        return $this->packs->firstWhere('id', $this->form->service_pack_id);
    }

    #[Computed]
    public function bookingStatus()
    {
        if ($this->action === 'edit') {
            return $this->form->status;
        }

        return BookingStatus::PENDING;
    }

    #[Computed]
    public function paymentStatus()
    {
        if ($this->action === 'edit') {
            return $this->form->booking->payment->status;
        }

        return PaymentStatus::PENDING;
    }

    #[Computed]
    public function availabilities()
    {
        if (empty($this->availability_date)) {
            return [];
        }

        $availabilities = $this->calendar->availabilities()
            ->where('date', $this->availability_date)
            ->when(!is_null($this->form->service_pack_id), function($query) {
                $query->where(function($query) {
                    $query
                        ->doesntHave('packs')
                        ->orWhereHas('packs', function($query) {
                            $query->where('id', $this->form->service_pack_id);
                        });
                });
            })
            ->withCount([
                'bookings' => function ($query) {
                    $query
                        ->select('id', 'calendar_availability_id', 'status')
                        ->whereNotIn('status', [BookingStatus::CANCELLED, BookingStatus::EXPIRED]);
                }
            ])
            ->with('packs:id,service_id')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $availabilities = $availabilities
            ->map(fn($availability) => [
                'id' => $availability->id,
                'name' => $availability->start_time->format('H:i'),
                'disabled' => $availability->bookings_count >= $availability->capacity,
                'services' => $availability->packs->pluck('service_id')->toArray(),
            ]);

        return $availabilities->toArray();
    }

    #[Computed]
    public function availabilityConfig()
    {
        $dates = $this->calendar->availabilities()
            ->select('date')
            ->when(!is_null($this->form->service_pack_id), function($query) {
                $query->where(function($query) {
                    $query
                        ->doesntHave('packs')
                        ->orWhereHas('packs', function($query) {
                            $query->where('id', $this->form->service_pack_id);
                        });
                });
            })
            ->distinct()
            ->get()
            ->pluck('date')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->toArray();

        return [
            'locale' => 'es',
            'altFormat' =>  'd/m/Y',
            'dateFormat' => 'Y-m-d',
            'enable' => $dates,
        ];
    }

    /**
     * methods
     */
    public function submit()
    {
        if ($this->action === 'create') {
            $questions = collect($this->questions)
                ->where('answer', '!=', '')
                ->map(fn($question) => [
                    'calendar_question_id' => $question['id'],
                    'answer' => $question['answer'],
                ])
                ->toArray();

            DB::transaction(function () use ($questions) {
                $service_pack = $this->pack;

                // 1. crear reserva
                $this->form->manual = true;
                $booking = $this->form->store();

                // 2. crear pago
                $booking->payment()->create([
                    'payment_method_id' => PaymentMethod::BANK_TRANSFER,
                    'status' => PaymentStatus::PENDING,
                    'amount' => $service_pack->reservation_price,
                ]);
                // 3. crear respuestas
                $booking->answers()->createMany($questions);
            });

            $this->success('Reserva creada correctamente', css: 'bg-primary text-white');
        }

        if ($this->action === 'edit') {
            $questions = array_filter($this->questions, function ($question) {
                return !is_null($question['booking_answer_id']) || !empty($question['answer']);
            });

            DB::transaction(function () use ($questions) {
                // 1. actualizar reserva
                $booking = $this->form->update();
                // 2. actualizar pago
                $booking->payment->update([
                    'amount' => $this->pack->reservation_price,
                ]);
                // 3. actaulizar respuestas
                foreach ($questions as $question) {
                    if (!is_null($question['booking_answer_id'])) {
                        $booking->answers()
                            ->where('id', $question['booking_answer_id'])
                            ->update([
                                'answer' => $question['answer'],
                            ]);
                        continue;
                    }

                    $booking->answers()->create([
                        'calendar_question_id' => $question['id'],
                        'answer' => $question['answer'],
                    ]);
                }
            });

            $this->success('Reserva actualizada correctamente', css: 'bg-primary text-white');
        }

        $this->open = false;
        $this->action = 'create';
        $this->resetDrawer();

        $this->dispatch('bookings:updated');
    }

    public function delete()
    {
        if ($this->action !== 'edit') {
            return;
        }

        $this->action = 'create';
        $this->form->delete();
        
        $this->open = false;
        $this->resetDrawer();

        $this->dispatch('bookings:updated');
        $this->success('Reserva eliminada correctamente', css: 'bg-primary text-white');
    }

    private function loadCalendarData(): void
    {
        if (is_null($this->calendar_id)) {
            return;
        }

        $this->calendar = Calendar::query()
            ->select('id')
            ->with([
                'services', 'services.packs',
                'questions' => fn($query) => $query->where('is_active', true)->orderBy('position'),
                'questions.services:id',
            ])
            ->findOrFail($this->calendar_id);

        unset($this->services);
    }

    public function updatedFormServiceId()
    {
        $this->form->service_pack_id = null;
        $this->loadServiceQuestions();
    }

    public function loadServiceQuestions()
    {
        $answers = collect();
        if ($this->action === 'edit') {
            $answers = $this->form->booking->answers;
        }


        $this->questions_key = uniqid('booking-questions-');
        $this->questions = $this->calendar
            ->questions
            ->filter(fn($question) => $question->services->where('id', $this->form->service_id)->isNotEmpty())
            ->map(function ($question) use ($answers) {
                $answer = $answers->firstWhere('calendar_question_id', $question->id);

                return [
                    'id' => $question->id,
                    'question' => $question->question,
                    'required' => $question->is_required,
                    'position' => $question->position,
                    'services' => $question->services->pluck('id')->toArray(),
                    'answer' => $answer?->answer ?? '',
                    'booking_answer_id' => $answer?->id,
                ];
            })
            ->values()
            ->toArray();
    }

    public function pending()
    {
        $payment = $this->form->booking->payment;
        $payment->status = PaymentStatus::PENDING;
        $payment->status_changed_at = now();
        $payment->save();

        $this->form->status = BookingStatus::PENDING;
        $this->form->update();

        $this->dispatch('bookings:updated');
        $this->success('Reserva marcada como pendiente correctamente', css: 'bg-primary text-white');
    }

    public function cancel()
    {
        $payment = $this->form->booking->payment;
        $payment->status = PaymentStatus::FAILED;
        $payment->status_changed_at = now();
        $payment->save();

        $this->form->status = BookingStatus::CANCELLED;
        $this->form->update();

        $this->dispatch('bookings:updated');
        $this->success('Reserva cancelada correctamente', css: 'bg-primary text-white');
    }

    public function confirmPayment()
    {
        $payment = $this->form->booking->payment;
        $payment->status = PaymentStatus::COMPLETED;
        $payment->status_changed_at = now();
        $payment->save();

        $this->form->status = BookingStatus::CONFIRMED;
        $this->form->update();

        $this->form->booking->client->notify(new ClientConfirmed($this->form->booking));

        $this->dispatch('bookings:updated');
        $this->success('Reserva confirmada correctamente', css: 'bg-primary text-white');
    }

    public function cancelPayment()
    {
        $payment = $this->form->booking->payment;
        $payment->status = PaymentStatus::PENDING;
        $payment->status_changed_at = now();
        $payment->save();

        $this->form->status = BookingStatus::PENDING;
        $this->form->update();

        $this->dispatch('bookings:updated');
        $this->success('Pago actualizado correctamente', css: 'bg-primary text-white');
    }

    public function searchClients(string $text = '')
    {
        $selected_client = Client::query()
            ->where('id', $this->form->client_id)
            ->take(1)
            ->get();

        $this->clients = Client::query()
            ->where('tenant_id', Auth::id())
            ->when(!empty($text), function ($query) use ($text) {
                $query->where(function ($query) use ($text) {
                    $query
                        ->where('name', 'like', "%$text%")
                        ->orWhere('lastname', 'like', "%$text%")
                        ->orWhere('email', 'like', "%$text%")
                        ->orWhere('phone_number', 'like', "%$text%");
                });
            })
            ->orderBy('name')
            ->orderBy('lastname')
            ->take(15)
            ->get()
            ->merge($selected_client)
            ->map(function ($client) {
                $sublabel = $client->email;
                if (!empty($client->phone_number)) {
                    $sublabel .= ' - '.$client->phone_number;
                }

                return [
                    'id' => $client->id,
                    'name' => $client->fullname,
                    'sublabel' => $sublabel
                ];
            });
    }

    public function updatedAvailabilityDate($value, $old)
    {
        $this->form->calendar_availability_id = null;
    }

    public function updatedFormCalendarAvailabilityId($value)
    {
        $this->availability = null;
        unset($this->services);
    }

    /**
     * render
     */
    public function render()
    {
        return view('livewire.components.dashboard.bookings.drawer');
    }
}
