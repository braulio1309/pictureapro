<div class="dashboard-container">
    <!-- Filtros -->
    <!-- Tarjetas de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Tarjeta 1 -->
        <div class="stats-card bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-500">Clientes Totales</h3>
            <p class="text-3xl font-bold">{{ $stats['totalClients'] }}</p>
        </div>
        
        <!-- Tarjeta 2 -->
        <div class="stats-card bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-500">Nuevos Clientes</h3>
            <p class="text-3xl font-bold">{{ $stats['newClients'] }}</p>
            <p class="text-sm text-gray-500">últimos {{ $stats['daydiff']  }} días</p>
        </div>
        
         
        <div class="stats-card bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-500">Reservas pendientes</h3>
            <p class="text-3xl font-bold">{{ $stats['PendingBookings'] }}</p>
            <p class="text-sm text-gray-500">{{ number_format(($stats['PendingBookings']/$stats['bookings'])*100, 1) }}% del total</p>
        </div>

        <div class="stats-card bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-500">Reservas confirmadas</h3>
            <p class="text-3xl font-bold">{{ $stats['ConfirmedBookings'] }}</p>
            <p class="text-sm text-gray-500">{{ number_format(($stats['ConfirmedBookings']/$stats['bookings'])*100, 1) }}% del total</p>
        </div>
        
    </div>

    <!-- Gráficos (sección siguiente) -->
</div>