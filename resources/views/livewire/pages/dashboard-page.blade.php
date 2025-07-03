<div>
    <x-header title="Bienvenido 🎉" separator progress-indicator />

    <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Panel de Control</h1>
    
    <!-- Componente de Estadísticas -->
    <livewire:dashboard.stats-overview />
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        <!-- Gráfico de crecimiento -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Crecimiento de Clientes</h2>
            <livewire:dashboard.charts />
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Data de reservas</h2>
            <livewire:dashboard.charts-booking />
        </div>
        
    </div>
</div>
</div>
