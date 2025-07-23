<div>
  <div wire:ignore>
    <canvas id="myChart"></canvas>
  </div>
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endassets

@script
<script>
  let myChart = null;
  let chartInitialized = false;

  // Función para inicializar o reiniciar el gráfico
  function initChart() {
    const ctx = document.getElementById('myChart');
    if (!ctx) {
      console.warn('Canvas element not found');
      return;
    }
    
    // Destruir el gráfico anterior si existe
    if (myChart) {
      myChart.destroy();
      myChart = null;
    }

    // Verificar si $wire está disponible y tiene los datos
    if (typeof $wire === 'undefined' || !$wire.clientGrowthData) {
      console.warn('$wire component or data not ready');
      return;
    }

    const data = $wire.clientGrowthData;
    if (!data || data.length === 0) {
      console.warn('No hay datos para mostrar el gráfico');
      return;
    }

    try {
      myChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: data.map(item => item.Day),
          datasets: [{
            label: 'Crecimiento de clientes',
            data: data.map(item => item.Value),
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.3
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
      chartInitialized = true;
      console.log('Gráfico inicializado correctamente');
    } catch (error) {
      console.error('Error al inicializar el gráfico:', error);
    }
  }

  // Función para manejar actualizaciones de datos
  function updateChartData(payload) {
    if (!myChart || !chartInitialized) {
      initChart();
      return;
    }

    try {
      myChart.data.labels = payload.data.map(item => item.Day);
      myChart.data.datasets[0].data = payload.data.map(item => item.Value);
      myChart.update();
      console.log('Gráfico actualizado correctamente');
    } catch (error) {
      console.error('Error al actualizar el gráfico:', error);
      initChart(); // Recrear el gráfico si falla la actualización
    }
  }

  // Inicialización segura cuando todo esté listo
  function initializeWhenReady() {
    if (document.readyState === 'complete') {
      initChart();
    } else {
      document.addEventListener('DOMContentLoaded', initChart);
    }

    // Manejar $wire específicamente
    if (window.$wire) {
      $wire.hook('message.processed', (message) => {
        setTimeout(() => {
          if (document.getElementById('myChart')) {
            initChart();
          }
        }, 50);
      });

      $wire.on('chart-updated', (payload) => {
        console.log('Datos actualizados recibidos:', payload);
        updateChartData(payload);
      });

      $wire.on('refreshChart', () => {
        console.log('Refrescando gráfico manualmente...');
        initChart();
      });
    } else {
      console.warn('$wire no está disponible');
      //setTimeout(initializeWhenReady, 100);
    }
  }

  // Iniciar el proceso
  initializeWhenReady();

  // Manejar navegación SPA
  document.addEventListener('$wire:navigated', () => {
    initChart();
  });

  // Manejar visibilidad de pestaña
  document.addEventListener('visibilitychange', () => {
    if (!document.hidden && document.getElementById('myChart')) {
      initChart();
    }
  });
</script>
@endscript