<template>
  <Card class="p-6">
    <h3 class="mb-4 font-bold">{{ title }}</h3>
    <p v-if="!rows.length" class="text-sm text-gray-500">Нет данных за выбранный период.</p>
    <canvas v-else ref="canvas" height="140"></canvas>
  </Card>
</template>

<script>
import { Chart } from 'chart.js/auto'

export default {
  props: {
    title: { type: String, required: true },
    rows: { type: Array, required: true }, // [{ label, sessions }]
  },

  data() {
    return { chart: null }
  },

  watch: {
    rows() {
      this.render()
    },
  },

  mounted() {
    this.render()
  },

  beforeUnmount() {
    this.chart?.destroy()
  },

  methods: {
    render() {
      this.chart?.destroy()

      if (!this.rows.length) {
        return
      }

      this.chart = new Chart(this.$refs.canvas, {
        type: 'bar',
        data: {
          labels: this.rows.map((row) => row.label),
          datasets: [
            {
              label: 'Сессии',
              data: this.rows.map((row) => row.sessions),
              backgroundColor: '#6366f1',
            },
          ],
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          plugins: { legend: { display: false } },
          scales: { x: { beginAtZero: true } },
        },
      })
    },
  },
}
</script>
