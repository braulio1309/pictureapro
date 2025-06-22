<?php

namespace App\Livewire\Pages\Dashboard\Calendars;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Calendar;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use App\Models\CalendarAvailability;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

class ShowPage extends Component
{
    #[Locked]
    public int $id;

    public Calendar $calendar;
    public string $calendar_view = 'dayGridMonth';
    public string $booking_status = '';

    public array $filters = [
        'from_now' => false,
        'start_date' => null,
        'end_date' => null,
    ];

    #[Locked]
    public array $datepicker_config = [
        'locale' => 'es',
        'dateFormat' => 'Y-m-d',
        'altFormat' => 'd/m/Y',
        'altInput' => true,
    ];

    public function mount()
    {
        $this->calendar = Calendar::findOrFail($this->id);
    }

    #[Renderless]
    public function openDrawer()
    {
        $this->dispatch('calendars:open-drawer', 'edit', $this->id);
    }

    #[Renderless]
    public function openOffBooking(?int $id = null)
    {
        if (!is_null($id)) {
            $this->dispatch(
                'bookings:open-drawer',
                type: 'no-availability',
                calendar: $this->id,
                action: 'edit',
                id: $id
            );

            return;
        }

        $this->dispatch(
            'bookings:open-drawer',
            type: 'no-availability',
            calendar: $this->id
        );
    }

    /**
     * methods
     */
    public function getEventSlots(Collection $availabilities): array
    {
        $events = [];

        foreach ($availabilities as $availability) {
            $start = new Carbon($availability->date->format('Y-m-d') . ' ' . $availability->start_time->format('H:i:s'));
            $end = new Carbon($availability->date->format('Y-m-d') . ' ' . $availability->end_time->format('H:i:s'));
            $intervalMinutes = $availability->duration;
            
            while ($start < $end) {
                $blockEnd = $start->copy()->addMinutes($intervalMinutes);
                $remainingCapacity = $availability->capacity;

                $events[] = [
                    'title' => $start->format('H:i'),
                    'start' => $start->toIso8601String(),
                    'end' => $blockEnd->toIso8601String(),
                    'backgroundColor' => '#28a745',
                    'borderColor' => '#28a745',
                    'capacity' => $remainingCapacity,
                ];

                $start->addMinutes($intervalMinutes);
            }
        }

        return $events;
    }

    public function updating($property, $value)
    {
        if (str_contains($property, 'filters.')) {
            $this->dispatch('calendars:filters-updated');
        }
    }

    /**
     * computed
     */
    #[Computed]
    public function calendarViews()
    {
        return [
            [
                'name' => 'Mes',
                'icon' => 'o-calendar',
                'value' => 'dayGridMonth',
                'active' => $this->calendar_view == 'dayGridMonth',
            ],
            [
                'name' => 'Semana',
                'icon' => 'o-calendar-days',
                'value' => 'timeGridWeek',
                'active' => $this->calendar_view == 'timeGridWeek',
            ],
            [
                'name' => 'Agenda',
                'icon' => 'o-bookmark',
                'value' => 'listWeek',
                'active' => $this->calendar_view == 'listWeek',
            ],
        ];
    }

    #[Computed]
    public function bookingStatuses()
    {
        return [
            [
                'name' => 'Todas',
                'icon' => 'o-calendar',
                'value' => '',
                'active' => $this->booking_status === '',
            ],
            [
                'name' => 'Pendientes',
                'icon' => 'o-clock',
                'value' => BookingStatus::PENDING->value,
                'active' => $this->booking_status === BookingStatus::PENDING->value,
            ],
            [
                'name' => 'Confirmadas',
                'icon' => 'o-check',
                'value' => BookingStatus::CONFIRMED->value,
                'active' => $this->booking_status === BookingStatus::CONFIRMED->value,
            ],
        ];
    }

    #[Computed]
    public function stats(): array
    {
        $availabilities = CalendarAvailability::query()
            ->with('bookings')
            ->where('calendar_id', $this->id)
            ->orderBy('date', 'asc')
            ->get();
    
        $available = $availabilities->filter(fn($availability) => $availability->bookings->isEmpty())->count();

        $confirmed = Booking::query()
            ->where('calendar_id', $this->id)
            ->where('status', BookingStatus::CONFIRMED)
            ->count();

        $pending = Booking::query()
            ->where('calendar_id', $this->id)
            ->where('status', BookingStatus::PENDING)
            ->count();

        $total = $confirmed + $pending + $available;
        
        return [
            'total' => $total,
            'confirmed' => $confirmed,
            'pending' => $pending,
            'available' => $available,
        ];
    }

    #[Computed]
    public function noAvailabilityBookings(): Collection
    {
        if ($this->calendar_view !== 'listWeek') {
            return collect();
        }

        $bookings = $this->calendar->bookings()
            ->whereNull('calendar_availability_id')
            ->when(!empty($this->booking_status), fn($query) => $query->where('status', $this->booking_status))
            ->with(['client', 'pack', 'pack.service', 'payment'])
            ->when($this->filters['from_now'], fn($query) => $query->whereDate('created_at', '>=', now()))
            ->when($this->filters['start_date'], fn($query) => $query->whereDate('created_at', '>=', $this->filters['start_date']))
            ->when($this->filters['end_date'], fn($query) => $query->whereDate('created_at', '<=', $this->filters['end_date']))
            ->latest('created_at')
            ->get();

        return $bookings;
    }

    #[Computed]
    public function withAvailabilityBookings(): Collection
    {
        if ($this->calendar_view !== 'listWeek') {
            return collect();
        }

        $bookings = $this->calendar->bookings()
            ->addSelect(['availability_date' => CalendarAvailability::select('date')
                ->whereColumn('calendar_availabilities.id', 'bookings.calendar_availability_id')
                ->limit(1)
            ])
            ->whereNotNull('calendar_availability_id')
            ->when(!empty($this->booking_status), fn($query) => $query->where('status', $this->booking_status))
            ->with(['availability', 'client', 'pack', 'pack.service', 'payment'])
            ->when($this->filters['from_now'], function($query) {
                return $query->whereHas('availability', fn($query) => $query->whereDate('date', '>=', now()));
            })
            ->when($this->filters['start_date'], function($query) {
                return $query->whereHas('availability', fn($query) => $query->whereDate('date', '>=', $this->filters['start_date']));
            })
            ->when($this->filters['end_date'], function($query) {
                return $query->whereHas('availability', fn($query) => $query->whereDate('date', '<=', $this->filters['end_date']));
            })
            ->orderBy('availability_date')
            ->get()
            ->map(function ($booking) {
                $booking->availability_date = ucfirst((new Carbon($booking->availability_date))->translatedFormat('F j, Y'));
                return $booking;
            })
            ->groupBy('availability_date');

        return $bookings;
    }

    /**
     * events
     */
    #[On('calendars:updated')]
    public function refreshList()
    {
        $this->calendar->refresh();
    }

    #[On('calendars:deleted')]
    public function checkIfDeleted(int $id)
    {
        if ($id == $this->id) {
            $this->redirect(route('dashboard.calendars.index'));
        }
    }

    #[On('calendars:change-view')]
    public function changeView(string $view)
    {
        $this->calendar_view = $view;
        $this->dispatch('calendars:change-calendar-view', view: $view);
    }

    #[On('calendars:availability-updated')]
    public function refreshStats(): void
    {
        unset($this->stats);
    }

    #[On('bookings:updated')]
    public function refreshBookings()
    {
        unset($this->noAvailabilityBookings);
    }

    #[On('bookings:change-status')]
    public function changeBookingStatus(string $status)
    {
        $this->booking_status = $status;
        $this->refreshBookings();
    }

    public function render()
    {
        return view('livewire.pages.dashboard.calendars.show-page');
    }
}
