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

    const ctx = document.getElementById('myChart');
    const subscriptions=$wire.clientGrowthData;

    const labels=subscriptions.map(item=>item.Day);
    const values=subscriptions.map(item=>item.Value);
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Crecimiento de clientes',
          data: values,
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

   
  </script>
@endscript