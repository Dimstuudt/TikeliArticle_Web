<script setup>
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import 'dayjs/locale/id'
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

dayjs.locale('id')
dayjs.extend(relativeTime)

const props = defineProps({
  articles: Object,
  latestUsers: Array,
})

const now = ref(dayjs())
let interval = null

onMounted(() => {
  interval = setInterval(() => {
    now.value = dayjs()
  }, 30000)

  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  clearInterval(interval)
  document.removeEventListener('click', handleClickOutside)
})

const goToPage = (url) => {
  if (url) {
    router.visit(url, {
      preserveScroll: true,
      preserveState: true,
    })
  }
}

// Search & Filter
const search = ref('')
const category = ref('')
const dropdownOpen = ref(false)

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
}

const selectCategory = (value) => {
  category.value = value
  dropdownOpen.value = false
}

const handleClickOutside = (e) => {
  if (!e.target.closest('.dropdown-wrapper')) {
    dropdownOpen.value = false
  }
}

const uniqueCategories = computed(() => {
  const all = props.articles.data.map(a => a.category).filter(Boolean)
  return [...new Set(all)]
})

const filteredArticles = computed(() => {
  return props.articles.data.filter(article => {
    const matchCategory = category.value ? article.category === category.value : true
    const matchSearch = search.value
      ? article.title.toLowerCase().includes(search.value.toLowerCase())
      : true
    return matchCategory && matchSearch
  })
})

import { Link } from '@inertiajs/vue3'
</script>

<template>
  <PublicLayout>
    <!-- Hero -->
    <section class="bg-blue-100 text-center py-12 px-4">
      <h2 class="text-3xl md:text-4xl font-bold text-blue-800 mb-3">Selamat Datang di Tikeli</h2>
      <p class="text-blue-700 text-lg max-w-2xl mx-auto">
        Temukan artikel terbaru dan terpercaya dari para penulis kami.
      </p>
    </section>

    <!-- Pengguna Berdasarkan Role -->
   <section class="mb-12">
  <div class="bg-white rounded-xl shadow-lg p-6">
    <h3 class="text-xl font-bold text-blue-700 text-center mb-6">Pengguna Terbaru</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <Link
        v-for="user in props.latestUsers"
        :key="user.id"
        :href="route('guest.profile', user.id)"
        class="block bg-gradient-to-br from-white to-blue-50 border border-blue-100 rounded-lg p-5 shadow hover:shadow-md transition-all text-center hover:scale-[1.01]"
      >
        <div class="w-20 h-20 mx-auto mb-3 relative">
          <div class="w-20 h-20 rounded-full border-4 border-blue-300 shadow-inner overflow-hidden">
            <img
              :src="user.profile_photo_path
                ? `/storage/${user.profile_photo_path}`
                : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random`"
              alt="Foto Profil"
              class="w-full h-full object-cover"
            />
          </div>
        </div>
        <p class="text-base font-semibold text-gray-800">{{ user.name }}</p>
        <span
          class="inline-block mt-2 px-3 py-1 text-xs font-semibold rounded-full"
          :class="{
            'bg-green-100 text-green-800 border border-green-300': user.role === 'admin',
            'bg-green-50 text-green-600 border border-green-200': user.role === 'operator',
          }"
        >
          {{ user.role }}
        </span>
      </Link>
    </div>
  </div>
</section>


    <!-- Artikel -->
    <main class="flex-grow max-w-7xl mx-auto px-4 py-10">
      <h3 class="text-2xl font-bold text-blue-700 mb-6">Artikel Terbaru</h3>

      <!-- Filter & Search -->
      <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <input
          v-model="search"
          type="text"
          placeholder="Cari artikel berdasarkan judul..."
          class="w-full md:w-1/2 px-4 py-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
        />

        <!-- Custom Dropdown -->
        <div class="relative w-full md:w-1/4 dropdown-wrapper" @click="toggleDropdown">
          <button
            class="w-full px-4 py-2 border rounded shadow-sm bg-white text-left focus:outline-none focus:ring-2 focus:ring-blue-300 flex justify-between items-center"
          >
            <span>{{ category || 'Semua Kategori' }}</span>
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <transition name="fade">
            <ul
              v-if="dropdownOpen"
              class="absolute z-10 mt-2 w-full bg-white border rounded shadow-lg"
            >
              <li
                @click.stop="selectCategory('')"
                class="px-4 py-2 hover:bg-blue-50 cursor-pointer"
              >
                Semua Kategori
              </li>
              <li
                v-for="cat in uniqueCategories"
                :key="cat"
                @click.stop="selectCategory(cat)"
                class="px-4 py-2 hover:bg-blue-50 cursor-pointer"
              >
                {{ cat }}
              </li>
            </ul>
          </transition>
        </div>
      </div>

      <!-- Grid Artikel -->
      <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="article in filteredArticles"
          :key="article.id"
          class="bg-white rounded-xl shadow hover:shadow-lg transition flex flex-col overflow-hidden cursor-pointer"
          @click="router.visit(`/articles/${article.id}`)"
        >
          <div class="relative w-full h-48">
            <img
              :src="article.cover ?? 'https://via.placeholder.com/600x300?text=No+Cover'"
              alt="cover"
              class="w-full h-full object-cover"
            />
            <div
              v-if="article.category"
              class="absolute top-2 right-2 bg-indigo-600 text-white text-[10px] px-2 py-0.5 rounded-full shadow-sm uppercase"
            >
              {{ article.category }}
            </div>
          </div>

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

      <!-- Pagination -->
      <div class="flex justify-between items-center mt-10" v-if="props.articles.total > props.articles.per_page">
        <button
          @click="goToPage(props.articles.prev_page_url)"
          :disabled="!props.articles.prev_page_url"
          class="px-5 py-2.5 rounded-lg text-white font-medium shadow-md transition-all duration-300
                 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700
                 disabled:opacity-40 disabled:cursor-not-allowed"
        >
          ← Sebelumnya
        </button>

        <span class="text-gray-600 font-medium">
          Halaman {{ props.articles.current_page }} dari {{ props.articles.last_page }}
        </span>

        <button
          @click="goToPage(props.articles.next_page_url)"
          :disabled="!props.articles.next_page_url"
          class="px-5 py-2.5 rounded-lg text-white font-medium shadow-md transition-all duration-300
                 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700
                 disabled:opacity-40 disabled:cursor-not-allowed"
        >
          Selanjutnya →
        </button>
      </div>


    </main>
  </PublicLayout>
</template>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}
</style>
