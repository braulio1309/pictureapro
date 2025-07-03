<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Client;
use App\Models\Booking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ChartsBooking extends Component
{
    public $bookingData;

    public function mount()
    {
        $this->bookingData = $this->prepareBookingData();
    }


    protected function prepareBookingData()
    {
        $data = [];
        $days = 30;
        $pending = Booking::whereHas('client', function($query) {
                    $query->where('tenant_id', Auth::id());
                })
                ->where('created_at', '>=', now()->subDays(30))
                ->with(['client', 'calendar']) // Carga las relaciones
                ->where('status', '=', 'pending')
                ->orderBy('start_time', 'desc')
                ->count();
        $confirmed = Booking::whereHas('client', function($query) {
                    $query->where('tenant_id', Auth::id());
                })
                ->where('created_at', '>=', now()->subDays(30))
                ->with(['client', 'calendar']) // Carga las relaciones
                ->where('status', '=', 'confirmed')
                ->orderBy('start_time', 'desc')->count();
        $completed = Booking::whereHas('client', function($query) {
                    $query->where('tenant_id', Auth::id());
                })
                ->where('created_at', '>=', now()->subDays(30))
                ->with(['client', 'calendar']) // Carga las relaciones
                ->where('status', '=', 'completed')
                ->orderBy('start_time', 'desc')->count();
         

        return [$pending, $confirmed, $completed];
    }

    public function render()
    {
        return view('livewire.dashboard.charts-booking');
    }
}
