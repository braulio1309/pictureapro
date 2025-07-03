<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class DashboardPage extends Component
{
    public $tenant;
    public $timeRange = '30'; // 7, 30, 90 días
    
    public function mount()
    {
        $this->tenant = Auth::user();
    }
    
    public function setTimeRange($days)
    {
        $this->timeRange = $days;
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
