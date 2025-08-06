<script setup>
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import 'dayjs/locale/id'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'

dayjs.locale('id')
dayjs.extend(relativeTime)

const props = defineProps({
  articles: Array,
})

const now = ref(dayjs())
let interval = null

onMounted(() => {
  interval = setInterval(() => {
    now.value = dayjs()
  }, 30000)
})

onBeforeUnmount(() => {
  clearInterval(interval)
})
</script>

<template>
  <div class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Navbar -->
    <header class="bg-blue-600 text-white py-4 px-6 shadow-md flex justify-between items-center">
      <h1 class="text-2xl font-bold tracking-wide">Tikeli</h1>
      <nav class="hidden md:flex space-x-6 text-sm"></nav>
      <a href="/login" class="text-sm hover:underline">Login / Register</a>
    </header>

    <!-- Hero -->
    <section class="bg-blue-100 text-center py-12 px-4">
      <h2 class="text-3xl md:text-4xl font-bold text-blue-800 mb-3">Selamat Datang di Tikeli</h2>
      <p class="text-blue-700 text-lg max-w-2xl mx-auto">
        Temukan artikel terbaru dan terpercaya dari para penulis kami.
      </p>
    </section>

    <!-- Artikel -->
    <main class="flex-grow max-w-7xl mx-auto px-4 py-10">
      <h3 class="text-2xl font-bold text-blue-700 mb-6">Artikel Terbaru</h3>

      <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="article in props.articles"
          :key="article.id"
          class="bg-white rounded-xl shadow hover:shadow-lg transition flex flex-col overflow-hidden cursor-pointer"
          @click="router.visit(`/articles/${article.id}`)"
        >
          <img
            :src="article.cover ?? 'https://via.placeholder.com/600x300?text=No+Cover'"
            alt="cover"
            class="w-full h-48 object-cover"
          />
          <div class="p-5 flex flex-col flex-grow">
            <span class="text-xs text-blue-500 uppercase font-semibold mb-1">Artikel</span>
            <h4 class="text-lg font-semibold text-gray-800 mb-2">
              {{ article.title }}
            </h4>
            <div class="text-sm text-gray-600 line-clamp-3 flex-grow">
              {{ article.summary ?? 'Tidak ada ringkasan' }}
            </div>
            <div class="text-xs text-gray-400 mt-4">
              {{ article.author?.name ?? 'Anonim' }} •
              {{
                article.updated_at
                  ? dayjs(article.updated_at).from(now.value)
                  : article.created_at
                  ? dayjs(article.created_at).from(now.value)
                  : 'Waktu tidak diketahui'
              }}
            </div>
          </div>
        </div>
      </div>

      <!-- CTA -->
      <div class="mt-16 text-center">
        <p class="text-gray-700">
          Belum punya akun?
          <a href="/register" class="text-blue-600 hover:underline font-semibold">Daftar sekarang</a>
        </p>
      </div>
    </main>
  </div>
</template>
