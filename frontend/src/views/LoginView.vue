<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { apiError } from '../utils/format'

const auth = useAuthStore()
const router = useRouter()

const form = reactive({
  email: 'demo@garagebook.test',
  password: 'password123',
})

const loading = ref(false)
const error = ref('')

async function submit() {
  loading.value = true
  error.value = ''

  try {
    await auth.login(form)
    router.push('/')
  } catch (err) {
    error.value = apiError(err)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="grid min-h-screen lg:grid-cols-2">
    <section
      class="relative hidden overflow-hidden border-r border-white/5 bg-slate-900/30 p-12 lg:flex lg:flex-col lg:justify-between"
    >
      <div class="pointer-events-none absolute -top-28 -right-28 h-96 w-96 rounded-full bg-amber-400/10 blur-3xl"></div>
      <div class="pointer-events-none absolute -bottom-40 -left-24 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>

      <div class="relative flex items-center gap-2.5">
        <span class="grid h-9 w-9 place-items-center rounded-xl bg-amber-400 text-slate-950">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            class="h-5 w-5"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M5 16.5h14M6.2 16.5l-1-3.2a2 2 0 0 1 .1-1.3l1.5-3.2A2 2 0 0 1 8.6 7.6h6.8a2 2 0 0 1 1.8 1.2l1.5 3.2a2 2 0 0 1 .1 1.3l-1 3.2"
            />
            <circle cx="7.5" cy="16.5" r="1.6" />
            <circle cx="16.5" cy="16.5" r="1.6" />
          </svg>
        </span>
        <span class="text-lg font-black tracking-tight text-white">
          Garage<span class="text-amber-400">Book</span>
        </span>
      </div>

      <div class="relative max-w-lg">
        <p class="text-xs font-bold uppercase tracking-[0.35em] text-amber-400">Your car, in order</p>
        <h2 class="mt-4 text-4xl font-black leading-tight text-white xl:text-5xl">
          每一次保養，<br />都讓愛車多陪你一程。
        </h2>
        <p class="mt-5 text-sm leading-relaxed text-slate-400">
          保養、加油、花費與提醒，全部收在同一個車庫手帳裡。不再翻找紙本、不再忘記換機油。
        </p>

        <div class="mt-10 grid grid-cols-3 gap-4">
          <div class="rounded-xl border border-white/5 bg-white/[0.02] p-4">
            <p class="text-xl font-black text-amber-400">保養</p>
            <p class="mt-1 text-xs text-slate-500">歷程完整記錄</p>
          </div>
          <div class="rounded-xl border border-white/5 bg-white/[0.02] p-4">
            <p class="text-xl font-black text-amber-400">油耗</p>
            <p class="mt-1 text-xs text-slate-500">自動幫你計算</p>
          </div>
          <div class="rounded-xl border border-white/5 bg-white/[0.02] p-4">
            <p class="text-xl font-black text-amber-400">提醒</p>
            <p class="mt-1 text-xs text-slate-500">到期不再錯過</p>
          </div>
        </div>
      </div>

      <p class="relative text-xs text-slate-600">Laravel · Vue 3 · MySQL</p>
    </section>

    <section class="flex items-center justify-center px-5 py-10 sm:px-8">
      <div class="w-full max-w-sm">
        <div class="mb-8 flex items-center gap-2.5 lg:hidden">
          <span class="grid h-9 w-9 place-items-center rounded-xl bg-amber-400 text-slate-950">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.8"
              class="h-5 w-5"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M5 16.5h14M6.2 16.5l-1-3.2a2 2 0 0 1 .1-1.3l1.5-3.2A2 2 0 0 1 8.6 7.6h6.8a2 2 0 0 1 1.8 1.2l1.5 3.2a2 2 0 0 1 .1 1.3l-1 3.2"
              />
              <circle cx="7.5" cy="16.5" r="1.6" />
              <circle cx="16.5" cy="16.5" r="1.6" />
            </svg>
          </span>
          <span class="text-lg font-black tracking-tight text-white">
            Garage<span class="text-amber-400">Book</span>
          </span>
        </div>

        <p class="text-sm font-semibold text-amber-400">歡迎回來</p>
        <h1 class="mt-1 text-2xl font-black text-white">登入你的車庫手帳</h1>
        <p class="mt-1 text-sm text-slate-400">
          還沒有帳號？
          <router-link to="/register" class="font-semibold text-amber-400 hover:text-amber-300">
            建立一個
          </router-link>
        </p>

        <form class="mt-7 space-y-4" @submit.prevent="submit">
          <div>
            <label class="label" for="login-email">電子郵件</label>
            <input
              id="login-email"
              v-model="form.email"
              type="email"
              class="input"
              autocomplete="email"
              required
            />
          </div>

          <div>
            <label class="label" for="login-password">密碼</label>
            <input
              id="login-password"
              v-model="form.password"
              type="password"
              class="input"
              autocomplete="current-password"
              required
            />
          </div>

          <p
            v-if="error"
            class="rounded-xl border border-rose-500/20 bg-rose-500/10 px-3 py-2 text-xs text-rose-300"
          >
            {{ error }}
          </p>

          <button type="submit" class="btn btn-primary w-full" :disabled="loading">
            {{ loading ? '登入中…' : '登入' }}
          </button>
        </form>

        <div class="mt-6 rounded-xl border border-white/5 bg-white/[0.02] p-4 text-xs text-slate-400">
          <p class="font-semibold text-slate-300">示範帳號（已替你填入）</p>
          <p class="mt-1.5 font-mono text-slate-300">demo@garagebook.test</p>
          <p class="font-mono text-slate-300">password123</p>
        </div>
      </div>
    </section>
  </div>
</template>
