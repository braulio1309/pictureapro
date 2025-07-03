<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Client;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends Component
{
    public $timeRange = '30'; // Días por defecto
    
    // Método para cambiar el rango de tiempo
    public function setTimeRange($days)
    {
        $this->timeRange = $days;
    }

    public function render()
    {
        $bookings = Booking::whereHas('client', function($query) {
                    $query->where('tenant_id', Auth::id());
                })
                ->where('created_at', '>=', now()->subDays($this->timeRange))
                ->with(['client', 'calendar']) // Carga las relaciones
                ->orderBy('start_time', 'desc')
                ->count();
        $pending = Booking::whereHas('client', function($query) {
                    $query->where('tenant_id', Auth::id());
                })
                ->where('created_at', '>=', now()->subDays($this->timeRange))
                ->with(['client', 'calendar']) // Carga las relaciones
                ->where('status', '=', 'pending')
                ->orderBy('start_time', 'desc')
                ->count();
        $confirmed = Booking::whereHas('client', function($query) {
                    $query->where('tenant_id', Auth::id());
                })
                ->where('created_at', '>=', now()->subDays($this->timeRange))
                ->with(['client', 'calendar']) // Carga las relaciones
                ->where('status', '=', 'confirmed')
                ->orderBy('start_time', 'desc')
                ->count();
        $stats = [
            'totalClients' => Client::where('tenant_id', '=', Auth::user()->id)->count(),
            'newClients' => Client::where('created_at', '>=', now()->subDays($this->timeRange))->count(),
            'publishingClients' => Client::where('allow_publish_images', true)->count(),
            'PendingBookings' => $pending,
            'bookings' => ($bookings == 0)? 1: $bookings,
            'ConfirmedBookings' => $confirmed,

        ];

        return view('livewire.dashboard.stats-overview', compact('stats'));
    }
}