<script setup lang="ts">
import { Pie } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  CategoryScale,
  type ChartData
} from 'chart.js'
import { computed } from 'vue'

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale)

interface Props {
  chartData: ChartData<'pie'>
  title?: string
}

const props = defineProps<Props>()

const options = computed(() => ({
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
}))
</script>

<template>
  <Pie :data="chartData" :options="options" />
</template>
