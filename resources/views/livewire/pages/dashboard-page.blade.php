<div>
    <x-header title="Bienvenido 🎉" separator progress-indicator />

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Panel de Control</h1>

        <div class="flex gap-2 mb-6">
            <button wire:click="setTimeRange('7')" class="btn btn-accent btn-ouline text-white rounded-3xl">7 días</button>
            <button wire:click="setTimeRange('30')" class="btn btn-primary btn-ouline text-white rounded-3xl">30 días</button>
            <button wire:click="setTimeRange('90')" class="btn btn-accent btn-ouline text-white rounded-3xl">90 días</button>

            <div class="w-full sm:w-1/3">
                <x-input
                    label="Desde"
                    icon="o-calendar"
                    inline
                    class="rounded-3xl"
                    wire:model.live="startDate"
                    type="date" />
            </div>
            <div class="w-full sm:w-1/3">
                <x-input
                    label="Hasta"
                    icon="o-calendar"
                    inline
                    class="rounded-3xl"
                    wire:model.live="endDate"
                    type="date" />
            </div>

        </div>
        <livewire:dashboard.stats-overview :startDate="$startDate"
            :endDate="$endDate" />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Crecimiento de Clientes</h2>
                <livewire:dashboard.charts :start-date="$startDate"
                    :end-date="$endDate" :key="'charts-'.$startDate.'-'.$endDate" />
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Data de reservas</h2>
                <livewire:dashboard.charts-booking :startDate="$startDate"
                    :endDate="$endDate" />
            </div>
        </div>
    </div>
</div>