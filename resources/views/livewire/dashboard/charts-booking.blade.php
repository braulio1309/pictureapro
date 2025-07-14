<div>

  <div wire:ignore>
    <canvas id="mysecondchart"></canvas>
  </div>

</div>


@assets
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endassets

@script
<script>

  document.addEventListener('livewire:initialized', () => {
    const ctx = document.getElementById('mysecondchart');
    const chartData = $wire.bookingData;
    console.log(Object.values(chartData.data))
    
    const chart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: Object.values(chartData.labels),
        datasets: [{
          label: 'Reservas por Servicio',
          data: Object.values(chartData.data),
          backgroundColor: Object.values(chartData.colors),
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'right',
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return `${context.label}: ${context.raw} reservas`;
              }
            }
          }
        }
      }
    });
    
    
  });
  
</script>
@endscript