<div wire:ignore>
  <canvas id="mysecondchart"></canvas>
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endassets

@script
<script>
  let bookingChart = null;

  // Función para inicializar el gráfico
  function initBookingChart() {
    const ctx = document.getElementById('mysecondchart');
    if (!ctx) return;

    if (bookingChart) {
      bookingChart.destroy();
    }

    const data = $wire.bookingData;

    bookingChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: data.labels,
        datasets: [{
          label: 'Reservas por Servicio',
          data: data.data,
          backgroundColor: data.colors,
          borderColor: data.colors.map(color => color.replace('rgb', 'rgba').replace(')', ', 1)')),
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return `${context.parsed.y} reservas`;
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              precision: 0
            }
          }
        }
      }
    });
  }

  // Inicializar al cargar la página
  document.addEventListener('DOMContentLoaded', initBookingChart);
  
  // Manejar la navegación SPA de Livewire
  document.addEventListener('livewire:navigated', initBookingChart);
  
  // Manejar actualizaciones de datos
  $wire.on('booking-chart-updated', (payload) => {
    if (!payload?.data) return;

    if (bookingChart) {
      bookingChart.data.labels = payload.data.labels;
      bookingChart.data.datasets[0].data = payload.data.data;
      bookingChart.data.datasets[0].backgroundColor = payload.data.colors;
      bookingChart.data.datasets[0].borderColor = payload.data.colors.map(color =>
        color.replace('rgb', 'rgba').replace(')', ', 1)'));
      bookingChart.update();
    } else {
      initBookingChart();
    }
  });

  // Reiniciar el chart cuando Livewire actualice el DOM
  Livewire.hook('message.processed', (message) => {
    // Verificar si el canvas existe en el nuevo DOM
    if (document.getElementById('mysecondchart') && !bookingChart) {
      initBookingChart();
    }
  });

  // Manejar cuando la pestaña vuelve a estar visible
  document.addEventListener('visibilitychange', () => {
    if (!document.hidden && document.getElementById('mysecondchart')) {
      initBookingChart();
    }
  });
</script>
@endscript