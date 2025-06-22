<?php

namespace App\Listeners;

use App\Models\Booking;
use App\Models\Calendar;
use App\Events\CalendarServicesUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckCalendarAvailabilitiesPacks
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CalendarServicesUpdated $event): void
    {
        $calendar = Calendar::query()
            ->with(['services', 'services.packs', 'availabilities', 'availabilities.packs'])
            ->findOrFail($event->calendarId);

        $service_packs = $calendar->services
            ->pluck('packs')
            ->collapse()
            ->pluck('id')
            ->toArray();

        $calendar->availabilities->each(function ($availability) use ($service_packs) {
            $packs = $availability->packs->pluck('id')->toArray();
            $sync = array_intersect($service_packs, $packs);

            if (empty($packs) && empty($sync)) {
                return;
            }

            $availability->packs()->sync($sync);
        });

        // eliminar reservas con servicios no vinculados
        $bookings = Booking::query()
            ->select('id')
            ->where('calendar_id', $calendar->id)
            ->whereNotIn('service_pack_id', $service_packs)
            ->delete();
    }
}
