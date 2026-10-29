<script setup>
import { onMounted, ref } from 'vue'
import http from '../api/http'
import { useAuthStore } from '../stores/auth'
import { money } from '../utils/format'
import StatCard from '../components/StatCard.vue'
import SpendChart from '../components/SpendChart.vue'
import ReminderList from '../components/ReminderList.vue'
import CarCard from '../components/CarCard.vue'

const auth = useAuthStore()

const stats = ref(null)
const loading = ref(true)
const error = ref('')

const todayText = new Date().toLocaleDateString('zh-TW', {
  year: 'numeric',
  month: 'long',
  day: 'numeric',
  weekday: 'long',
})

async function load() {
  loading.value = true
  error.value = ''

  try {
    const { data } = await http.get('/stats')
    stats.value = data
  } catch {
    error.value = '目前無法載入車庫資料，請稍後再試。'
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<template>
  <div>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="text-sm font-semibold text-amber-400">車庫總覽</p>
        <h1 class="mt-1 text-2xl font-black text-white sm:text-3xl">
          你好，{{ auth.user?.name || '車主' }}
        </h1>
        <p class="mt-1 text-sm text-slate-400">{{ todayText }} · 愛車的每筆花費都在掌握之中</p>
      </div>
      <router-link to="/cars" class="btn btn-primary self-start sm:self-auto">
        ＋ 管理車庫
      </router-link>
    </div>

    <div v-if="error" class="card mt-6 p-8 text-center">
      <p class="text-slate-300">{{ error }}</p>
      <button class="btn btn-primary mt-4" @click="load">重新載入</button>
    </div>

    <div v-else-if="loading" class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div v-for="item in 4" :key="item" class="h-32 animate-pulse rounded-2xl bg-slate-900/60"></div>
    </div>

    <template v-else-if="stats">
      <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <StatCard
          label="總花費"
          :value="money(stats.total_cost)"
          hint="保養與加油累計支出"
          icon="wallet"
        />
        <StatCard
          label="本月花費"
          :value="money(stats.month_cost)"
          hint="本月累計支出"
          icon="calendar"
        />
        <StatCard
          label="平均油耗"
          :value="stats.avg_consumption ? `${stats.avg_consumption} km/L` : '—'"
          hint="依加油紀錄自動估算"
          icon="fuel"
        />
        <StatCard
          label="車輛數"
          :value="`${stats.cars_count} 台`"
          :hint="stats.reminders.length ? `${stats.reminders.length} 項待處理提醒` : '目前沒有待處理提醒'"
          icon="car"
        />
      </div>

      <div class="mt-4 grid gap-4 lg:grid-cols-3">
        <section class="card p-5 lg:col-span-2">
          <div class="flex items-center justify-between gap-3">
            <div>
              <h2 class="text-base font-bold text-white">近六個月花費</h2>
              <p class="mt-0.5 text-xs text-slate-500">保養與加油的每月支出</p>
            </div>
            <span class="chip">NT$</span>
          </div>
          <div class="mt-4">
            <SpendChart :monthly="stats.monthly" />
          </div>
        </section>

        <section class="card p-5">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-base font-bold text-white">保養提醒</h2>
            <span
              v-if="stats.reminders.length"
              class="rounded-full bg-amber-400/15 px-2 py-0.5 text-[11px] font-bold text-amber-300"
            >
              {{ stats.reminders.length }}
            </span>
          </div>
          <div class="mt-4">
            <ReminderList :reminders="stats.reminders" />
          </div>
        </section>
      </div>

      <div class="mt-8">
        <div class="flex items-end justify-between gap-3">
          <div>
            <h2 class="text-lg font-bold text-white">我的車輛</h2>
            <p class="mt-0.5 text-xs text-slate-500">點選車輛查看完整紀錄</p>
          </div>
          <router-link to="/cars" class="text-xs font-semibold text-amber-400 hover:text-amber-300">
            管理車庫 →
          </router-link>
        </div>

        <div v-if="!stats.cars.length" class="card mt-4 flex flex-col items-center justify-center px-6 py-14 text-center">
          <span class="grid h-14 w-14 place-items-center rounded-2xl bg-amber-400/10 text-amber-400">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.7"
              class="h-7 w-7"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M4.5 16.5h15M5.8 16.5l-1-3.3a2 2 0 0 1 .1-1.3l1.6-3.4A2 2 0 0 1 8.3 7.3h7.4a2 2 0 0 1 1.8 1.2l1.6 3.4a2 2 0 0 1 .1 1.3l-1 3.3"
              />
              <circle cx="7.6" cy="16.6" r="1.6" />
              <circle cx="16.4" cy="16.6" r="1.6" />
            </svg>
          </span>
          <h3 class="mt-4 text-base font-bold text-white">還沒有任何車輛</h3>
          <p class="mt-1 max-w-sm text-sm text-slate-400">
            新增你的第一台車，開始記錄保養、加油與花費。
          </p>
          <router-link to="/cars" class="btn btn-primary mt-5">新增第一台車</router-link>
        </div>

        <div v-else class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <CarCard v-for="car in stats.cars" :key="car.id" :car="car" />
        </div>
      </div>
    </template>
  </div>
</template>
