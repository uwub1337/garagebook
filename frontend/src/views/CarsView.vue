<script setup>
import { onMounted, ref } from 'vue'
import http from '../api/http'
import { apiError, fuelTypeLabel, km, money } from '../utils/format'
import CarFormModal from '../components/CarFormModal.vue'

const cars = ref([])
const loading = ref(true)
const error = ref('')
const showForm = ref(false)
const editing = ref(null)

async function load() {
  loading.value = true
  error.value = ''

  try {
    const { data } = await http.get('/cars')
    cars.value = data
  } catch {
    error.value = '目前無法載入車庫資料，請稍後再試。'
  } finally {
    loading.value = false
  }
}

onMounted(load)

function createCar() {
  editing.value = null
  showForm.value = true
}

function editCar(car) {
  editing.value = car
  showForm.value = true
}

function closeForm() {
  showForm.value = false
  editing.value = null
}

async function removeCar(car) {
  const confirmed = window.confirm(`確定要刪除「${car.name}」嗎？所有保養與加油紀錄也會一併刪除。`)
  if (!confirmed) return

  try {
    await http.delete(`/cars/${car.id}`)
    await load()
  } catch (err) {
    window.alert(apiError(err))
  }
}
</script>

<template>
  <div>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="text-sm font-semibold text-amber-400">車庫管理</p>
        <h1 class="mt-1 text-2xl font-black text-white sm:text-3xl">我的車庫</h1>
        <p class="mt-1 text-sm text-slate-400">目前共有 {{ cars.length }} 台車</p>
      </div>
      <button class="btn btn-primary self-start sm:self-auto" @click="createCar">
        ＋ 新增車輛
      </button>
    </div>

    <div v-if="error" class="card mt-6 p-8 text-center">
      <p class="text-slate-300">{{ error }}</p>
      <button class="btn btn-primary mt-4" @click="load">重新載入</button>
    </div>

    <div v-else-if="loading" class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <div v-for="item in 3" :key="item" class="h-72 animate-pulse rounded-2xl bg-slate-900/60"></div>
    </div>

    <div
      v-else-if="!cars.length"
      class="card mt-6 flex flex-col items-center justify-center px-6 py-16 text-center"
    >
      <span class="grid h-16 w-16 place-items-center rounded-2xl bg-amber-400/10 text-amber-400">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="1.7"
          class="h-8 w-8"
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
      <h2 class="mt-4 text-lg font-bold text-white">車庫還是空的</h2>
      <p class="mt-1 max-w-sm text-sm text-slate-400">
        建立第一台車，開始記錄保養與加油，讓每一筆花費都有跡可循。
      </p>
      <button class="btn btn-primary mt-5" @click="createCar">新增第一台車</button>
    </div>

    <div v-else class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <article v-for="car in cars" :key="car.id" class="card group overflow-hidden">
        <router-link :to="`/cars/${car.id}`" class="block">
          <div
            class="relative h-40 overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-slate-950"
          >
            <img
              v-if="car.photo_url"
              :src="car.photo_url"
              :alt="car.name"
              class="h-full w-full object-cover opacity-90 transition duration-500 group-hover:scale-105"
            />
            <div v-else class="absolute inset-0 grid place-items-center">
              <span class="text-6xl font-black text-white/5">{{ car.name.slice(0, 1) }}</span>
            </div>

            <span
              class="absolute top-3 left-3 rounded-full bg-slate-950/70 px-2.5 py-1 text-[11px] font-semibold text-amber-300 backdrop-blur"
            >
              {{ fuelTypeLabel[car.fuel_type] || car.fuel_type }}
            </span>

            <span
              v-if="car.reminders?.length"
              class="absolute top-3 right-3 rounded-full bg-rose-500 px-2 py-0.5 text-[11px] font-bold text-white"
            >
              {{ car.reminders.length }} 項提醒
            </span>
          </div>

          <div class="p-4">
            <p class="text-base font-bold text-white">{{ car.name }}</p>
            <p class="text-xs text-slate-400">
              {{ car.make }} {{ car.model }}<span v-if="car.year"> · {{ car.year }}</span>
            </p>

            <div class="mt-3 grid grid-cols-3 gap-2 border-t border-white/5 pt-3 text-center">
              <div>
                <p class="text-[10px] text-slate-500">里程</p>
                <p class="mt-0.5 text-xs font-semibold text-slate-200">{{ km(car.current_mileage) }}</p>
              </div>
              <div>
                <p class="text-[10px] text-slate-500">總花費</p>
                <p class="mt-0.5 text-xs font-semibold text-amber-400">{{ money(car.total_cost) }}</p>
              </div>
              <div>
                <p class="text-[10px] text-slate-500">油耗</p>
                <p class="mt-0.5 text-xs font-semibold text-slate-200">
                  {{ car.avg_consumption ? `${car.avg_consumption} km/L` : '—' }}
                </p>
              </div>
            </div>
          </div>
        </router-link>

        <div class="flex gap-2 border-t border-white/5 p-3">
          <router-link :to="`/cars/${car.id}`" class="btn btn-ghost flex-1 py-2 text-xs">
            查看詳情
          </router-link>
          <button class="btn btn-ghost px-3 py-2 text-xs" @click="editCar(car)">編輯</button>
          <button class="btn btn-danger px-3 py-2 text-xs" @click="removeCar(car)">刪除</button>
        </div>
      </article>
    </div>

    <CarFormModal :open="showForm" :car="editing" @close="closeForm" @saved="load" />
  </div>
</template>
