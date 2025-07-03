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
  const ctx2 = document.getElementById('mysecondchart');
  const data2 = $wire.bookingData;
  console.log(data2)

  new Chart(ctx2, {
    type: 'doughnut',
    data: {
      labels: ['Pendientes', 'Confirmados', 'Completados'],
      datasets: [{
        label: 'Crecimiento de clientes',
        data: data2,
        backgroundColor: [
          'rgb(255, 205, 86)',
          'rgb(40, 170, 231)',
          'rgb(49, 242, 104)',
        ],
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