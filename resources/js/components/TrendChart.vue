<template>
  <Card class="p-6">
    <h3 class="mb-4 font-bold">Трафик</h3>
    <canvas ref="canvas" height="90"></canvas>
  </Card>
</template>

<script>
import { Chart } from 'chart.js/auto'

export default {
  props: {
    series: { type: Array, required: true },
  },

  data() {
    return { chart: null }
  },

  watch: {
    series() {
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

      this.chart = new Chart(this.$refs.canvas, {
        type: 'line',
        data: {
          labels: this.series.map((row) => row.date),
          datasets: [
            {
              label: 'Просмотры',
              data: this.series.map((row) => row.pageviews),
              borderColor: '#6366f1',
              backgroundColor: 'rgba(99, 102, 241, 0.15)',
              tension: 0.3,
              fill: true,
            },
            {
              label: 'Уникальные посетители',
              data: this.series.map((row) => row.visitors),
              borderColor: '#10b981',
              backgroundColor: 'rgba(16, 185, 129, 0.15)',
              tension: 0.3,
              fill: true,
            },
          ],
        },
        options: {
          responsive: true,
          interaction: { mode: 'index', intersect: false },
          scales: { y: { beginAtZero: true } },
        },
      })
    },
  },
}
</script>
