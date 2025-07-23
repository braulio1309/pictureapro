<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Client;
use App\Models\Booking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChartsBooking extends Component
{
    public $bookingData = [];
    public $startDate;
    public $endDate;
    
    protected $listeners = ['dateRangeUpdated' => 'handleDateRangeChange'];

    public function mount($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->prepareBookingData();
    }

    public function handleDateRangeChange($dates)
    {
        $this->startDate = $dates['startDate'];
        $this->endDate = $dates['endDate'];
        $this->prepareBookingData();
        
        // Disparar evento para actualizar el gráfico
        $this->dispatch('booking-chart-updated', data: $this->bookingData);
    }

    protected function prepareBookingData()
    {
        $servicesData = DB::table('bookings')
            ->select(
                'services.name as service_name',
                DB::raw('COUNT(bookings.id) as total_bookings')
            )
            ->join('service_packs', 'bookings.service_pack_id', '=', 'service_packs.id')
            ->join('services', 'service_packs.service_id', '=', 'services.id')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('clients')
                    ->whereColumn('clients.id', 'bookings.client_id')
                    ->where('clients.tenant_id', Auth::id());
            })
            ->whereBetween('bookings.created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('total_bookings')
            ->get();

        $this->bookingData = [
            'labels' => $servicesData->pluck('service_name')->toArray(),
            'data' => $servicesData->pluck('total_bookings')->toArray(),
            'colors' => $this->generateChartColors($servicesData->count())
        ];
    }

    private function generateChartColors($count)
    {
        $baseColors = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 206, 86)',
            'rgb(75, 192, 192)',
            'rgb(153, 102, 255)',
            'rgb(255, 159, 64)',
            'rgb(199, 199, 199)',
            'rgb(83, 102, 255)',
            'rgb(255, 99, 255)'
        ];

        return array_slice(
            array_merge(...array_fill(0, ceil($count / count($baseColors)), $baseColors)),
            0,
            $count
        );
    }

    public function render()
    {
        return view('livewire.dashboard.charts-booking');
    }
}