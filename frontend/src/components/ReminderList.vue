<script setup>
defineProps({
  reminders: { type: Array, default: () => [] },
  showCar: { type: Boolean, default: true },
})
</script>

<template>
  <div v-if="reminders.length" class="space-y-2.5">
    <div
      v-for="reminder in reminders"
      :key="reminder.id"
      class="flex items-start gap-3 rounded-xl border px-3.5 py-3"
      :class="
        reminder.overdue
          ? 'border-rose-500/20 bg-rose-500/5'
          : 'border-amber-400/20 bg-amber-400/5'
      "
    >
      <span
        class="mt-1.5 h-2 w-2 shrink-0 rounded-full"
        :class="reminder.overdue ? 'bg-rose-400' : 'bg-amber-400'"
      ></span>

      <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-semibold text-white">
          {{ reminder.item }}
          <span v-if="showCar" class="text-xs font-normal text-slate-400">
            · {{ reminder.car_name }}
          </span>
        </p>
        <p class="mt-0.5 text-xs text-slate-400">
          <span v-if="reminder.due_date">到期日 {{ reminder.due_date }}</span>
          <span v-if="reminder.due_date && reminder.km_left !== null"> · </span>
          <span v-if="reminder.km_left !== null">
            距下次 {{ reminder.km_left.toLocaleString('zh-TW') }} km
          </span>
        </p>
      </div>

      <span
        class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-bold"
        :class="
          reminder.overdue
            ? 'bg-rose-500/15 text-rose-300'
            : 'bg-amber-400/15 text-amber-300'
        "
      >
        {{ reminder.overdue ? '已逾期' : '即將到期' }}
      </span>
    </div>
  </div>

  <div v-else class="flex flex-col items-center justify-center rounded-xl border border-dashed border-white/10 px-4 py-10 text-center">
    <span class="grid h-10 w-10 place-items-center rounded-full bg-emerald-500/10 text-emerald-400">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        class="h-5 w-5"
      >
        <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
      </svg>
    </span>
    <p class="mt-3 text-sm text-slate-400">目前沒有保養提醒，車況良好。</p>
  </div>
</template>
