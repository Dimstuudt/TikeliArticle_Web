<script>
import dayjs from "dayjs"
import "dayjs/locale/id"

dayjs.locale("id")

const formatDate = (date) => {
  return date ? dayjs(date).format("D MMMM YYYY") : "Unknown"
}
</script>

<template>
  <div class="relative w-full max-w-md">
    <div class="relative">
      <!-- Icon Search -->
      <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z"/>
        </svg>
      </span>

      <!-- Input -->
      <input
        type="text"
        v-model="query"
        placeholder="Cari user..."
        @input="onInput"
        class="w-full border border-gray-300 rounded-lg px-10 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
      />

      <!-- Tombol Clear -->
      <button
        v-if="query"
        @click="clearInput"
        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <transition name="fade">
      <div
        v-if="query.length"
        class="absolute bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg mt-1 w-full shadow-lg z-20 overflow-hidden max-h-72 overflow-y-auto"
      >
        <!-- Loading state dengan animasi gelombang -->
        <template v-if="loading">
          <div class="p-3 text-gray-500 dark:text-gray-400 flex items-center gap-1">
            <span>Searching</span>
            <span class="wave-dot" v-for="n in 3" :key="n"></span>
          </div>
        </template>
<!-- Results -->
<template v-else-if="results.length">
  <div
    v-for="user in results"
    :key="user.id"
    class="p-4 border-b border-gray-200 dark:border-gray-700 cursor-pointer flex items-center gap-4 transition hover:bg-gray-50 dark:hover:bg-gray-700"
    @click="goToUser(user.id)"
  >
    <!-- Avatar -->
    <img
      :src="user.profile_photo_url || '/default-avatar.png'"
      class="h-12 w-12 rounded-full object-cover border border-gray-300 dark:border-gray-600 shadow-sm"
      alt="Profil"
    >

    <!-- Info -->
    <div class="flex-1 min-w-0">
      <!-- Name + Role Badge -->
      <div class="flex items-center gap-2">
        <p class="font-semibold text-gray-800 dark:text-gray-100 truncate">
          {{ user.name }}
        </p>
        <span
          v-if="user.role === 'admin'"
          class="px-2 py-0.5 text-xs font-medium rounded-full bg-gradient-to-r from-red-600 to-red-500 text-white shadow-sm"
        >
          Admin
        </span>
        <span
          v-else
          class="px-2 py-0.5 text-xs font-medium rounded-full bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-sm"
        >
          Operator
        </span>
      </div>

      <!-- Bio -->
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 truncate">
        {{ user.bio || 'Belum ada bio' }}
      </p>

      <!-- Joined Date -->
      <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
        Joined: {{ formatDate(user.created_at) }}
      </p>
    </div>
  </div>
</template>

        <!-- No results -->
        <template v-else>
          <div class="p-3 text-gray-500 dark:text-gray-400">
            No user found for "<span class="font-semibold">{{ query }}</span>"
          </div>
        </template>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const query = ref('')
const results = ref([])
const loading = ref(false)

let timeout
const onInput = () => {
  clearTimeout(timeout)

  if (!query.value.trim()) {
    results.value = []
    loading.value = false
    return
  }

  loading.value = true
  results.value = []

  timeout = setTimeout(async () => {
    try {
      const response = await axios.get('/api/search-users', { params: { q: query.value } })
      results.value = response.data
    } catch (e) {
      console.error(e)
      results.value = []
    } finally {
      setTimeout(() => {
        loading.value = false
      }, 300)
    }
  }, 300)
}

const clearInput = () => {
  query.value = ''
  results.value = []
  loading.value = false
}

const goToUser = (id) => {
  router.get(`/users/${id}`)
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Animasi gelombang untuk titik */
.wave-dot {
  display: inline-block;
  width: 6px;
  height: 6px;
  background-color: currentColor;
  border-radius: 50%;
  animation: wave 1s infinite;
}
.wave-dot:nth-child(1) { animation-delay: 0s; }
.wave-dot:nth-child(2) { animation-delay: 0.2s; }
.wave-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes wave {
  0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
  30% { transform: translateY(-5px); opacity: 1; }
}
</style>
