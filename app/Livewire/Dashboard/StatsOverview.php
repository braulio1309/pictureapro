<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Client;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StatsOverview extends Component
{
    public $timeRange = '30'; // Días por defecto
    public $startDate;
    public $endDate;
        
    protected $listeners = ['dateRangeUpdated' => 'updateDates'];

    
    public function updateDates($dates)
    {
        $this->startDate = $dates['startDate'];
        $this->endDate = $dates['endDate'];
        $this->render(); // Forzar actualización
    }
    
    public function mount()
    {
        $this->setDateRange($this->timeRange); // Inicializa con el rango por defecto
    }

    // Método para cambiar el rango de tiempo (7/30/90 días)
    public function setTimeRange($days)
    {
        $this->timeRange = $days;
        $this->setDateRange($days); // Actualiza las fechas
    }

    // Método interno para establecer el rango de fechas
    protected function setDateRange($days)
    {
        $this->startDate = now()->subDays($days)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    // Actualización automática cuando cambian las fechas
    public function updated()
    {
        // Si se modifican los inputs manualmente, actualiza timeRange
        if ($this->startDate && $this->endDate) {
            $this->timeRange = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate));
        }
        
        // Validación para que endDate no sea menor que startDate
        if (Carbon::parse($this->startDate)->gt(Carbon::parse($this->endDate))) {
            $this->endDate = $this->startDate;
        }
    }

    public function render()
    {
        $bookings = Booking::whereHas('client', function($query) {
                    $query->where('tenant_id', Auth::id());
                })
                ->whereBetween('created_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ])
                ->with(['client', 'calendar'])
                ->orderBy('start_time', 'desc')
                ->count();

        $pending = Booking::whereHas('client', function($query) {
                    $query->where('tenant_id', Auth::id());
                })
                ->whereBetween('created_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ])
                ->where('status', 'pending')
                ->count();

        $confirmed = Booking::whereHas('client', function($query) {
                    $query->where('tenant_id', Auth::id());
                })
                ->whereBetween('created_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ])
                ->where('status', 'confirmed')
                ->count();

        $stats = [
            'totalClients' => Client::where('tenant_id', Auth::user()->id)->count(),
            'newClients' => Client::whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])->count(),
            'publishingClients' => Client::where('allow_publish_images', true)->count(),
            'PendingBookings' => $pending,
            'bookings' => ($bookings == 0) ? 1 : $bookings,
            'ConfirmedBookings' => $confirmed,
            'daydiff' => Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate))
        ];

        return view('livewire.dashboard.stats-overview', compact('stats'));
    }
}