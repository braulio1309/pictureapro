<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Charts extends Component
{
    public $clientGrowthData = [];
    public $startDate;
    public $endDate;
    
    protected $listeners = ['dateRangeUpdated' => 'handleDateRangeChange'];

    public function mount($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->prepareChartData();
    }

    public function handleDateRangeChange($dates)
    {
        $this->startDate = $dates['startDate'];
        $this->endDate = $dates['endDate'];
        $this->prepareChartData();        
        
        $this->dispatch('chart-updated', data: $this->clientGrowthData);
    }

    protected function prepareChartData()
    {
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $days = $start->diffInDays($end);
        
        $step = $days > 90 ? 7 : 1;

        $this->clientGrowthData = [];
        
        for ($i = 0; $i <= $days; $i += $step) {
            $date = $start->copy()->addDays($i);
            $this->clientGrowthData[] = [
                'Day' => $date->format('M d'),
                'Value' => Client::where('tenant_id', Auth::id())
                    ->whereDate('created_at', '=', $date)
                    ->count()
            ];
        }
    }

    public function render()
    {
        return view('livewire.dashboard.charts');
    }
}