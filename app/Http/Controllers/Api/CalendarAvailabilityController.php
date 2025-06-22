<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Calendar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CalendarAvailability;
use App\Http\Requests\Api\CalendarAvailabilityRequest;

class CalendarAvailabilityController extends Controller
{
    public function index(CalendarAvailabilityRequest $request, int $id)
    {
        $data = $request->validated();
        $filters = $data['filters'];

        $availabilities = CalendarAvailability::query()
            ->when(!empty($filters['from_now']), fn($query) => $query->whereDate('date', '>=', now()))
            ->when(!empty($filters['start_date']), fn($query) => $query->whereDate('date', '>=', $filters['start_date']))
            ->when(!empty($filters['end_date']), fn($query) => $query->whereDate('date', '<=', $filters['end_date']))
            ->with(['calendar', 'bookings'])
            ->where('calendar_id', $id)
            ->get();

        $events = [];

        foreach ($availabilities as $availability) {
            $start = $availability->date->setTimeFromTimeString($availability->start_time->format('H:i:s'));
            $end = $availability->date->setTimeFromTimeString($availability->end_time->format('H:i:s'));

            $bookings = $availability->bookings->count();
            $remaining_capacity = $availability->capacity - $bookings;

            if ($bookings > 0) {
                $availability->bookings->each(function ($booking) use (&$events, $start, $end, $availability) {
                    $events[] = [
                        'id' => 'availability-' . $availability->calendar->tenant_id . '-' . $availability->id,
                        'title' => $start->format('H:i'),
                        'start' => $start->toIso8601String(),
                        'end' => $end->toIso8601String(),
                        'status' => $booking->status->value,
                        'calendar_id' => $availability->calendar_id,
                        'booking_id' => $booking->id,
                        'calendar_availability_id' => $availability->id,
                    ];
                });
            }

            if ($remaining_capacity > 0) {
                $events[] = [
                    'id' => 'availability-' . $availability->calendar->tenant_id . '-' . $availability->id,
                    'title' => $start->format('H:i'),
                    'start' => $start->toIso8601String(),
                    'end' => $end->toIso8601String(),
                    'status' => 'available',
                    'calendar_id' => $availability->calendar_id,
                    'booking_id' => null,
                    'calendar_availability_id' => $availability->id,
                ];
            }
        }

        return response()->json($events);
    }

    public function public(CalendarAvailabilityRequest $request, int $id)
    {
        $calendar = Calendar::query()->findOrFail($id);
        $data = $request->validated();
        $filters = $data['filters'] ?? [];

        $availabilities = CalendarAvailability::query()
            ->where('calendar_id', $id)
            ->whereDate('date', '>=', now())
            ->when(!$calendar->show_busy, fn($query) => $query->with('bookings'))
            ->when($calendar->show_busy, fn($query) => $query->doesntHave('bookings'))
            ->when(!empty($filters['pack']), function($query) use ($filters) {
                $query->where(function($query) use ($filters) {
                    $query
                        ->doesntHave('packs')
                        ->orWhereHas('packs', function($query) use ($filters) {
                            $query->where('id', $filters['pack']);
                        });
                });
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $events = [];

        foreach ($availabilities as $availability) {
            $start = $availability->date->setTimeFromTimeString($availability->start_time->format('H:i:s'));
            $end = $availability->date->setTimeFromTimeString($availability->end_time->format('H:i:s'));


            $bookings = $availability->relationLoaded('bookings')
                ? $availability->bookings->count()
                : 0;

            $remaining_capacity = $availability->capacity - $bookings;

            if ($availability->relationLoaded('bookings') && $bookings > 0) {
                $availability->bookings->each(function ($booking) use (&$events, $start, $end, $availability) {
                    $events[] = [
                        'id' => 'calendar-availability-' . $availability->id,
                        'title' => $start->format('H:i'),
                        'start' => $start->toIso8601String(),
                        'end' => $end->toIso8601String(),
                        'status' => 'confirmed',
                        'calendar_id' => $availability->calendar_id,
                        'calendar_availability_id' => $availability->id,
                        'available' => false,
                    ];
                });
            }

            if ($remaining_capacity > 0) {
                $events[] = [
                    'id' => 'calendar-availability-' . $availability->id,
                    'title' => $start->format('H:i'),
                    'start' => $start->toIso8601String(),
                    'end' => $end->toIso8601String(),
                    'status' => 'available',
                    'calendar_id' => $availability->calendar_id,
                    'calendar_availability_id' => $availability->id,
                    'available' => true,
                ];
            }
        }

        return response()->json($events);
    }
}
