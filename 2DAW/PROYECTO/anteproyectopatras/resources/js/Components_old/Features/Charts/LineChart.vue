<template>
  <Line :data="data" :options="options" />
</template>

<script setup>
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
)

const props = defineProps({
  data: {
    type: Object,
    required: true
  },
  options: {
    type: Object,
    default: () => ({
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
            display: false
        }
      },
      scales: {
        y: {
            beginAtZero: false,
            grid: {
                color: '#f3f4f6'
            }
        },
        x: {
            grid: {
                display: false
            }
        }
      }
    })
  }
})
</script>
