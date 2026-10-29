<script setup>
import Chart from 'chart.js/auto'
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'

const props = defineProps({
  monthly: { type: Array, default: () => [] },
})

const canvas = ref(null)
let chart = null

function render() {
  if (!canvas.value) return

  chart?.destroy()
  chart = new Chart(canvas.value, {
    type: 'bar',
    data: {
      labels: props.monthly.map((item) => item.label),
      datasets: [
        {
          label: '保養',
          data: props.monthly.map((item) => item.maintenance),
          backgroundColor: 'rgba(251, 191, 36, 0.85)',
          borderRadius: 6,
          stack: 'cost',
        },
        {
          label: '加油',
          data: props.monthly.map((item) => item.fuel),
          backgroundColor: 'rgba(56, 189, 248, 0.65)',
          borderRadius: 6,
          stack: 'cost',
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          labels: { color: '#cbd5e1', usePointStyle: true, boxWidth: 8, boxHeight: 8 },
        },
        tooltip: {
          callbacks: {
            label: (ctx) => `${ctx.dataset.label}：NT$ ${Number(ctx.raw).toLocaleString('zh-TW')}`,
          },
        },
      },
      scales: {
        x: {
          stacked: true,
          grid: { display: false },
          ticks: { color: '#94a3b8' },
        },
        y: {
          stacked: true,
          grid: { color: 'rgba(148, 163, 184, 0.12)' },
          ticks: {
            color: '#94a3b8',
            callback: (value) => `NT$ ${Number(value).toLocaleString('zh-TW')}`,
          },
        },
      },
    },
  })
}

onMounted(render)
watch(() => props.monthly, render, { deep: true })
onBeforeUnmount(() => chart?.destroy())
</script>

<template>
  <div class="h-64 sm:h-72">
    <canvas ref="canvas"></canvas>
  </div>
</template>
