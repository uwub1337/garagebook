<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import http from '../api/http'
import { apiError, dateText, fuelTypeLabel, km, liters, money } from '../utils/format'
import ReminderList from '../components/ReminderList.vue'
import CarFormModal from '../components/CarFormModal.vue'
import MaintenanceFormModal from '../components/MaintenanceFormModal.vue'
import FuelFormModal from '../components/FuelFormModal.vue'

const route = useRoute()

const detail = ref(null)
const loading = ref(true)
const error = ref('')

const tab = ref('maintenance')
const showCarForm = ref(false)
const showMaintenance = ref(false)
const showFuel = ref(false)
const editingMaintenance = ref(null)
const editingFuel = ref(null)

const car = computed(() => detail.value?.car ?? null)
const stats = computed(() => detail.value?.stats ?? {})
const maintenance = computed(() => detail.value?.maintenance_records ?? [])
const fuel = computed(() => detail.value?.fuel_records ?? [])
const reminders = computed(() => detail.value?.stats?.reminders ?? [])

const tabs = computed(() => [
  { key: 'maintenance', label: '保養紀錄', count: maintenance.value.length },
  { key: 'fuel', label: '加油紀錄', count: fuel.value.length },
  { key: 'reminders', label: '保養提醒', count: reminders.value.length },
])

async function load() {
  loading.value = true
  error.value = ''

  try {
    const { data } = await http.get(`/cars/${route.params.id}`)
    detail.value = data
  } catch (err) {
    error.value =
      err.response?.status === 403
        ? '你沒有權限查看這台車。'
        : '找不到這台車，可能已經被刪除了。'
  } finally {
    loading.value = false
  }
}

onMounted(load)

function addRecord() {
  if (tab.value === 'maintenance') {
    editingMaintenance.value = null
    showMaintenance.value = true
  } else if (tab.value === 'fuel') {
    editingFuel.value = null
    showFuel.value = true
  }
}

function editMaintenance(record) {
  editingMaintenance.value = record
  showMaintenance.value = true
}

function editFuel(record) {
  editingFuel.value = record
  showFuel.value = true
}

function closeMaintenance() {
  showMaintenance.value = false
  editingMaintenance.value = null
}

function closeFuel() {
  showFuel.value = false
  editingFuel.value = null
}

async function removeMaintenance(record) {
  if (!window.confirm(`確定要刪除「${record.item}」這筆保養紀錄嗎？`)) return

  try {
    await http.delete(`/maintenance/${record.id}`)
    await load()
  } catch (err) {
    window.alert(apiError(err))
  }
}

async function removeFuel(record) {
  if (!window.confirm(`確定要刪除 ${dateText(record.date)} 的加油紀錄嗎？`)) return

  try {
    await http.delete(`/fuel/${record.id}`)
    await load()
  } catch (err) {
    window.alert(apiError(err))
  }
}

function nextDue(record) {
  const parts = []
  if (record.next_due_date) parts.push(dateText(record.next_due_date))
  if (record.next_due_mileage) parts.push(km(record.next_due_mileage))
  return parts.join(' · ')
}

function unitPrice(record) {
  const amount = Number(record.liters)
  return amount ? `NT$ ${Math.round(Number(record.cost) / amount)}/L` : ''
}
</script>

<template>
  <div>
    <router-link to="/cars" class="inline-flex items-center gap-1.5 text-sm text-slate-400 transition hover:text-white">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        class="h-4 w-4"
      >
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5l-7 7 7 7" />
      </svg>
      返回車庫
    </router-link>

    <div v-if="loading" class="mt-5 space-y-4">
      <div class="h-64 animate-pulse rounded-2xl bg-slate-900/60"></div>
      <div class="h-44 animate-pulse rounded-2xl bg-slate-900/60"></div>
    </div>

    <div v-else-if="error" class="card mt-5 p-10 text-center">
      <p class="text-slate-300">{{ error }}</p>
      <router-link to="/cars" class="btn btn-primary mt-4">回到我的車庫</router-link>
    </div>

    <template v-else-if="car">
      <section class="card mt-4 overflow-hidden">
        <div class="grid md:grid-cols-5">
          <div
            class="relative h-56 bg-gradient-to-br from-slate-800 via-slate-900 to-slate-950 md:col-span-2 md:h-auto md:min-h-72"
          >
            <img
              v-if="car.photo_url"
              :src="car.photo_url"
              :alt="car.name"
              class="absolute inset-0 h-full w-full object-cover"
            />
            <div v-else class="absolute inset-0 grid place-items-center">
              <span class="text-8xl font-black text-white/5">{{ car.name.slice(0, 1) }}</span>
            </div>
          </div>

          <div class="p-6 md:col-span-3">
            <div class="flex flex-wrap items-center gap-2">
              <span class="chip text-amber-300">{{ fuelTypeLabel[car.fuel_type] || car.fuel_type }}</span>
              <span v-if="car.plate" class="chip">{{ car.plate }}</span>
              <span v-if="car.year" class="chip">{{ car.year }}</span>
            </div>

            <div class="mt-3 flex flex-wrap items-start justify-between gap-3">
              <div>
                <h1 class="text-2xl font-black text-white sm:text-3xl">{{ car.name }}</h1>
                <p class="mt-0.5 text-sm text-slate-400">{{ car.make }} {{ car.model }}</p>
              </div>
              <button class="btn btn-ghost" @click="showCarForm = true">編輯車輛</button>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
              <div class="rounded-xl bg-white/[0.03] p-3">
                <p class="text-[11px] text-slate-500">目前里程</p>
                <p class="mt-1 text-sm font-bold text-white">{{ km(car.current_mileage) }}</p>
              </div>
              <div class="rounded-xl bg-white/[0.03] p-3">
                <p class="text-[11px] text-slate-500">保養花費</p>
                <p class="mt-1 text-sm font-bold text-white">{{ money(stats.maintenance_cost) }}</p>
              </div>
              <div class="rounded-xl bg-white/[0.03] p-3">
                <p class="text-[11px] text-slate-500">加油花費</p>
                <p class="mt-1 text-sm font-bold text-white">{{ money(stats.fuel_cost) }}</p>
              </div>
              <div class="rounded-xl bg-white/[0.03] p-3">
                <p class="text-[11px] text-slate-500">平均油耗</p>
                <p class="mt-1 text-sm font-bold text-white">
                  {{ stats.avg_consumption ? `${stats.avg_consumption} km/L` : '—' }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex rounded-xl border border-white/10 bg-slate-900/60 p-1">
          <button
            v-for="item in tabs"
            :key="item.key"
            class="rounded-lg px-3 py-2 text-xs font-semibold transition sm:px-3.5 sm:text-sm"
            :class="
              tab === item.key
                ? 'bg-amber-400 text-slate-950'
                : 'text-slate-400 hover:text-white'
            "
            @click="tab = item.key"
          >
            {{ item.label }}
            <span class="ml-1 opacity-70">{{ item.count }}</span>
          </button>
        </div>

        <button v-if="tab !== 'reminders'" class="btn btn-primary" @click="addRecord">
          {{ tab === 'maintenance' ? '＋ 新增保養' : '＋ 新增加油' }}
        </button>
        <button v-else class="btn btn-ghost" @click="showCarForm = true">編輯車輛</button>
      </div>

      <section class="mt-4">
        <div v-if="tab === 'maintenance'" class="space-y-2.5">
          <div v-if="!maintenance.length" class="card px-6 py-12 text-center text-sm text-slate-400">
            還沒有保養紀錄，
            <button class="text-amber-400 hover:text-amber-300" @click="addRecord">
              新增第一筆
            </button>
            。
          </div>

          <article
            v-for="record in maintenance"
            :key="record.id"
            class="card flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between"
          >
            <div class="min-w-0">
              <p class="font-semibold text-white">{{ record.item }}</p>
              <p class="mt-0.5 text-xs text-slate-400">
                {{ dateText(record.date) }} · {{ km(record.mileage) }}
              </p>
              <p v-if="nextDue(record)" class="mt-1 text-xs text-amber-400/90">
                下次：{{ nextDue(record) }}
              </p>
              <p v-if="record.notes" class="mt-1 text-xs text-slate-500">{{ record.notes }}</p>
            </div>

            <div class="flex shrink-0 items-center gap-2">
              <span class="mr-1.5 text-sm font-bold text-white">{{ money(record.cost) }}</span>
              <button class="btn btn-ghost px-3 py-1.5 text-xs" @click="editMaintenance(record)">
                編輯
              </button>
              <button class="btn btn-danger px-3 py-1.5 text-xs" @click="removeMaintenance(record)">
                刪除
              </button>
            </div>
          </article>
        </div>

        <div v-else-if="tab === 'fuel'" class="space-y-2.5">
          <div v-if="!fuel.length" class="card px-6 py-12 text-center text-sm text-slate-400">
            還沒有加油紀錄，
            <button class="text-amber-400 hover:text-amber-300" @click="addRecord">
              新增第一筆
            </button>
            。
          </div>

          <article
            v-for="record in fuel"
            :key="record.id"
            class="card flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between"
          >
            <div class="min-w-0">
              <p class="font-semibold text-white">
                {{ liters(record.liters) }}<span class="ml-2 text-xs font-normal text-slate-400">{{ unitPrice(record) }}</span>
              </p>
              <p class="mt-0.5 text-xs text-slate-400">
                {{ dateText(record.date) }} · {{ km(record.mileage) }}
              </p>
              <p v-if="record.notes" class="mt-1 text-xs text-slate-500">{{ record.notes }}</p>
            </div>

            <div class="flex shrink-0 items-center gap-2">
              <span class="mr-1.5 text-sm font-bold text-white">{{ money(record.cost) }}</span>
              <button class="btn btn-ghost px-3 py-1.5 text-xs" @click="editFuel(record)">
                編輯
              </button>
              <button class="btn btn-danger px-3 py-1.5 text-xs" @click="removeFuel(record)">
                刪除
              </button>
            </div>
          </article>
        </div>

        <div v-else class="card p-4">
          <ReminderList :reminders="reminders" :show-car="false" />
        </div>
      </section>

      <CarFormModal
        :open="showCarForm"
        :car="car"
        @close="showCarForm = false"
        @saved="load"
      />
      <MaintenanceFormModal
        :open="showMaintenance"
        :car-id="car.id"
        :record="editingMaintenance"
        @close="closeMaintenance"
        @saved="load"
      />
      <FuelFormModal
        :open="showFuel"
        :car-id="car.id"
        :record="editingFuel"
        @close="closeFuel"
        @saved="load"
      />
    </template>
  </div>
</template>
