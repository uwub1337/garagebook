<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { apiError } from '../utils/format'

const auth = useAuthStore()
const router = useRouter()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const loading = ref(false)
const error = ref('')

async function submit() {
  loading.value = true
  error.value = ''

  try {
    await auth.register(form)
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
        <p class="text-xs font-bold uppercase tracking-[0.35em] text-amber-400">Start your logbook</p>
        <h2 class="mt-4 text-4xl font-black leading-tight text-white xl:text-5xl">
          從今天開始，<br />好好照顧你的車。
        </h2>
        <p class="mt-5 text-sm leading-relaxed text-slate-400">
          建立帳號後就能新增車輛、記錄保養與加油，系統會自動整理花費與油耗，並在該保養時提醒你。
        </p>
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

        <p class="text-sm font-semibold text-amber-400">新車主你好</p>
        <h1 class="mt-1 text-2xl font-black text-white">建立你的車庫手帳</h1>
        <p class="mt-1 text-sm text-slate-400">
          已經有帳號了？
          <router-link to="/login" class="font-semibold text-amber-400 hover:text-amber-300">
            直接登入
          </router-link>
        </p>

        <form class="mt-7 space-y-4" @submit.prevent="submit">
          <div>
            <label class="label" for="register-name">暱稱</label>
            <input
              id="register-name"
              v-model="form.name"
              class="input"
              placeholder="怎麼稱呼你？"
              autocomplete="name"
              required
            />
          </div>

          <div>
            <label class="label" for="register-email">電子郵件</label>
            <input
              id="register-email"
              v-model="form.email"
              type="email"
              class="input"
              autocomplete="email"
              required
            />
          </div>

          <div>
            <label class="label" for="register-password">密碼（至少 8 個字元）</label>
            <input
              id="register-password"
              v-model="form.password"
              type="password"
              class="input"
              autocomplete="new-password"
              required
            />
          </div>

          <div>
            <label class="label" for="register-password-confirm">確認密碼</label>
            <input
              id="register-password-confirm"
              v-model="form.password_confirmation"
              type="password"
              class="input"
              autocomplete="new-password"
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
            {{ loading ? '建立中…' : '建立帳號' }}
          </button>
        </form>
      </div>
    </section>
  </div>
</template>
