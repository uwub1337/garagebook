<script setup>
defineProps({
  label: { type: String, required: true },
  value: { type: String, required: true },
  hint: { type: String, default: '' },
  icon: { type: String, default: 'car' },
})
</script>

<template>
  <div class="card p-5">
    <div class="flex items-start justify-between gap-3">
      <p class="text-xs font-medium tracking-wider text-slate-500">{{ label }}</p>
      <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-amber-400/10 text-amber-400">
        <svg
          v-if="icon === 'wallet'"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="1.7"
          class="h-4.5 w-4.5"
        >
          <rect x="3" y="6" width="18" height="13" rx="2.5" />
          <path stroke-linecap="round" d="M3 10h18M16.5 14.5h.01" />
        </svg>

        <svg
          v-else-if="icon === 'calendar'"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="1.7"
          class="h-4.5 w-4.5"
        >
          <rect x="3.5" y="5" width="17" height="15" rx="2.5" />
          <path stroke-linecap="round" d="M8 3v4M16 3v4M3.5 9.5h17" />
        </svg>

        <svg
          v-else-if="icon === 'fuel'"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="1.7"
          class="h-4.5 w-4.5"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.5s5.5 5.6 5.5 9.3a5.5 5.5 0 1 1-11 0C6.5 9.1 12 3.5 12 3.5Z" />
        </svg>

        <svg
          v-else
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="1.7"
          class="h-4.5 w-4.5"
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
    </div>

    <p class="mt-3 text-2xl font-black tracking-tight text-white">{{ value }}</p>
    <p v-if="hint" class="mt-1 text-xs text-slate-500">{{ hint }}</p>
  </div>
</template>
