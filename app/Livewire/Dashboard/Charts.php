<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Client;
use App\Models\Booking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Charts extends Component
{
    public $clientGrowthData;
    public $bookingData;

    public function mount()
    {
        $this->clientGrowthData = $this->prepareClientGrowthData();
        $this->bookingData = $this->prepareBookingData();
    }

    protected function prepareClientGrowthData()
    {
        $data = [];
        $days = 30;

        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = Client::whereDate('created_at', '<=', $date)
                ->where('tenant_id', Auth::user()->id)
                ->count();

            $data[] = [
                'Day' => $date->format('M d'),
                'Value' => $count,
            ];
        }

        return $data;
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
        return view('livewire.dashboard.charts');
    }
}
