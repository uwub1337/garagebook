<script setup>
import { fuelTypeLabel, km, money } from '../utils/format'

defineProps({
  car: { type: Object, required: true },
})
</script>

<template>
  <router-link
    :to="`/cars/${car.id}`"
    class="card group overflow-hidden transition hover:border-amber-400/30 hover:bg-slate-900"
  >
    <div class="relative h-36 overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-slate-950">
      <img
        v-if="car.photo_url"
        :src="car.photo_url"
        :alt="car.name"
        class="h-full w-full object-cover opacity-90 transition duration-500 group-hover:scale-105"
      />
      <div v-else class="absolute inset-0 grid place-items-center">
        <span class="text-6xl font-black text-white/5">{{ car.name.slice(0, 1) }}</span>
      </div>

      <span class="absolute left-3 top-3 rounded-full bg-slate-950/70 px-2.5 py-1 text-[11px] font-semibold text-amber-300 backdrop-blur">
        {{ fuelTypeLabel[car.fuel_type] || car.fuel_type }}
      </span>

      <span
        v-if="car.reminder_count"
        class="absolute right-3 top-3 rounded-full bg-rose-500 px-2 py-0.5 text-[11px] font-bold text-white"
      >
        {{ car.reminder_count }} 項提醒
      </span>
    </div>

    <div class="p-4">
      <p class="text-base font-bold text-white">{{ car.name }}</p>
      <p class="text-xs text-slate-400">
        {{ car.make }} {{ car.model }}<span v-if="car.year"> · {{ car.year }}</span>
      </p>

      <div class="mt-3 flex items-center justify-between border-t border-white/5 pt-3 text-xs">
        <span class="text-slate-500">{{ km(car.current_mileage) }}</span>
        <span class="font-semibold text-amber-400">{{ money(car.total_cost) }}</span>
      </div>
    </div>
  </router-link>
</template>
