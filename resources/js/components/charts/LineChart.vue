<script setup lang="ts">
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  type ChartData,
  type ChartOptions
} from 'chart.js'
import { computed } from 'vue'
import { merge } from 'lodash-es'

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale
)

interface Props {
  chartData: ChartData<'line'>
  chartOptions?: ChartOptions<'line'>
  title?: string
}

const props = defineProps<Props>()

const defaultOptions: ChartOptions<'line'> = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top' as const,
    },
    title: {
      display: !!props.title,
      text: props.title,
    },
  },
  scales: {
    y: {
      beginAtZero: true,
    },
  },
}

const options = computed(() => merge({}, defaultOptions, props.chartOptions))
</script>

<template>
  <Line :data="chartData" :options="options" />
</template>