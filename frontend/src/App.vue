<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from './stores/auth'

const auth = useAuthStore()
const router = useRouter()

onMounted(() => auth.fetchMe())

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}
</script>

<template>
  <div class="min-h-screen bg-slate-950">
    <div
      class="pointer-events-none fixed inset-x-0 top-0 -z-10 h-96 bg-[radial-gradient(ellipse_at_top,rgba(251,191,36,0.12),transparent_60%)]"
    ></div>

    <header
      v-if="auth.isLoggedIn"
      class="sticky top-0 z-40 border-b border-white/5 bg-slate-950/80 backdrop-blur"
    >
      <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
        <router-link to="/" class="flex items-center gap-2.5">
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
        </router-link>

        <nav class="flex items-center gap-1">
          <router-link to="/" class="nav-link" exact-active-class="nav-link-active">
            總覽
          </router-link>
          <router-link to="/cars" class="nav-link" active-class="nav-link-active">
            我的車庫
          </router-link>
          <div class="mx-2 hidden h-6 w-px bg-white/10 sm:block"></div>
          <span class="hidden max-w-32 truncate text-sm text-slate-400 sm:inline">
            {{ auth.user?.name }}
          </span>
          <button class="btn btn-ghost ml-2 px-3 py-1.5 text-xs" @click="handleLogout">
            登出
          </button>
        </nav>
      </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
      <router-view />
    </main>

    <footer
      v-if="auth.isLoggedIn"
      class="mx-auto max-w-6xl px-4 pb-10 text-center text-xs text-slate-600"
    >
      GarageBook 車庫手帳 · 讓每一趟旅程都有跡可循
    </footer>
  </div>
</template>
