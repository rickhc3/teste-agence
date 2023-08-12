<template>
  <div v-if="show" @close="closeModal">
    <div class="modal-content">
      <div class="flex flex-col md:flex-row">
        <div class="w-full md:w-2/3 pr-0 md:pr-4">
          <h3 class="text-md font-semibold mb-2">Performance Comercial</h3>
          <bar-chart :data="barChartData" :options="barChartOptions" />
        </div>
        <div class="w-full md:w-1/3 mt-4 md:mt-0">
          <h3 class="text-md font-semibold mb-2">Participação na Receita</h3>
          <pie-chart :data="pieChartData" :options="pieChartOptions" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    show: Boolean,
    barChartData: Object,
    pieChartData: Object,
  },
  data() {
    return {
      barChartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        tooltips: {
          callbacks: {
            label: function (tooltipItem, data) {
              var label = data.datasets[tooltipItem.datasetIndex].label || '';
              if (label) {
                label += ': ';
              }
              let value = tooltipItem.yLabel * 1000;
              label += value.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL',
              });
              return label;
            },
          },
        },
        scales: {
          yAxes: [
            {
              type: 'linear',
              display: true,
              position: 'left',
              id: 'y-axis-1',
              ticks: {
                beginAtZero: true,
                callback: function (value, index, values) {
                  value = value * 100;
                  return value.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL',
                  });
                },
              },
            }
          ],
        }
      },
      pieChartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        tooltips: {
          callbacks: {
            label: function (tooltipItem, data) {
              var label = data.labels[tooltipItem.index] || '';
              if (label) {
                label += ': ';
              }
              let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index] * 1000;
              label += value.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL',
              });
              return label;
            },
          },
        },
      },
    };
  },
  methods: {
    closeModal() {
      this.$emit("close-modal");
    },
  },
};
</script>
