<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardPage extends Component
{
    public $tenant;
    public $timeRange = '30'; 
    public $startDate;
    public $endDate;

    protected $listeners = ['dateRangeUpdated' => 'handleDateRangeUpdate'];


    public function mount()
    {
        // Valores por defecto (últimos 30 días)
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->timeRange = $this->timeRange;

    }

    // Método para cambiar el rango de fechas con los botones (7, 30, 90 días)
    public function setTimeRange($days)
    {
        $this->tenant = Auth::user();
        $this->startDate = now()->subDays($days)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->dispatchDateRangeUpdate();
    }

    public function updated($property)
    {
        if (in_array($property, ['startDate', 'endDate'])) {
            $this->timeRange = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate));
            $this->dispatchDateRangeUpdate();// Notificar a otros componentes
        }
    }

    protected function dispatchDateRangeUpdate()
    {
        $this->dispatch('dateRangeUpdated', [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
    }

    // Manejar actualización desde otros componentes
    public function handleDateRangeUpdate($dates)
    {
        $this->startDate = $dates['startDate'];
        $this->endDate = $dates['endDate'];
        $this->timeRange = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate));
    }
    
    // Estadísticas principales
    public function getStatsProperty()
    {
        return [
            'clients' => [
                'total' => $this->tenant->clients()->count(),
                'new' => $this->tenant->clients()
                    ->where('created_at', '>=', now()->subDays($this->timeRange))
                    ->count(),
                'with_services' => $this->tenant->clients()
                    ->has('services')
                    ->count(),
            ],
            'services' => [
                'total' => $this->tenant->services()->count(),
                'active' => $this->tenant->services()
                    ->where('is_active', true)
                    ->count(),
            ],
            'products' => [
                'total' => $this->tenant->products()->count(),
                'low_stock' => $this->tenant->products()
                    ->where('stock', '<', 5)
                    ->count(),
            ],
            'materials' => [
                'total' => $this->tenant->materials()->count(),
                'cost' => $this->tenant->materials()
                    ->sum('unit_cost'),
            ]
        ];
    }
    
    // Datos para gráficos
    public function getChartDataProperty()
    {
        $data = [];
        $days = $this->timeRange;
        
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data['labels'][] = $date->format('d M');
            $data['clients'][] = $this->tenant->clients()
                ->whereDate('created_at', '<=', $date)
                ->count();
            $data['services'][] = $this->tenant->services()
                ->whereDate('created_at', '<=', $date)
                ->count();
        }
        
        return $data;
    }
    
    // Elementos recientes
    public function getRecentItemsProperty()
    {
        return [
            'clients' => $this->tenant->clients()
                ->latest()
                ->take(5)
                ->get(),
            'services' => $this->tenant->services()
                ->with('client')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }
    public function render()
    {
        return view('livewire.pages.dashboard-page');
    }
}
