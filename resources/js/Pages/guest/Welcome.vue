<script setup>
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import 'dayjs/locale/id'
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

dayjs.locale('id')
dayjs.extend(relativeTime)

const props = defineProps({
  articles: Object,
  latestUsers: Array,
    topArticles: Array,
  filters: Object, // biar nilai search/category tetap setelah reload
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

// Pagination
const goToPage = (url) => {
  if (url) {
    router.visit(url, {
      preserveScroll: true,
      preserveState: true,
    })
  }
}

// Search & Filter (ambil dari props.filters)
const search = ref(props.filters?.search || '')
const category = ref(props.filters?.category || '')
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

// Submit filter ke server
const submitFilter = () => {
  router.visit(route('guest.welcome'), {
    method: 'get',
    data: {
      search: search.value,
      category: category.value
    },
    preserveScroll: true,
    preserveState: true
  })
}
</script>

<style>
.slide-fade-enter-active {
  transition: all 0.25s ease;
}
.slide-fade-leave-active {
  transition: all 0.2s ease;
}
.slide-fade-enter-from {
  opacity: 0;
  transform: translateY(-6px);
}
.slide-fade-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

@keyframes fade-up {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-up { animation: fade-up 1s ease forwards; }
.animation-delay-500 { animation-delay: 0.5s; }
.animation-delay-1000 { animation-delay: 1s; }
.animation-delay-2000 { animation-delay: 2s; }

@keyframes bounce-slow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-0.8px); } /* lebih tipis, hampir seperti getar halus */
}
.animate-bounce-slow {
  animation: bounce-slow 2s infinite; /* durasi lebih panjang → lebih smooth */
}


@keyframes blob {
  0%, 100% { transform: translate(0px,0px) scale(1); }
  33% { transform: translate(15px,-25px) scale(1.05); } /* gerakan lebih kecil */
  66% { transform: translate(-10px,10px) scale(0.95); }
}
.animate-blob {
  animation: blob 10s infinite; /* lebih lambat supaya efek halus */
}

html {
  scroll-behavior: smooth;
}

</style>
<template>
  <PublicLayout>


<!-- Hero Section -->
<section
  class="relative bg-gradient-to-br from-cyan-50 via-blue-50 to-blue-100
         dark:from-gray-900 dark:via-gray-800 dark:to-gray-900
         py-20 px-6 text-center overflow-hidden font-sans"
>
  <!-- Decorative background shapes -->
  <div
    class="absolute top-0 left-0 w-80 h-80 bg-cyan-200
           dark:bg-cyan-900
           rounded-full mix-blend-multiply filter blur-3xl opacity-25"
  ></div>
  <div
    class="absolute bottom-0 right-0 w-96 h-96 bg-blue-300
           dark:bg-blue-900
           rounded-full mix-blend-multiply filter blur-3xl opacity-25"
  ></div>

  <!-- Content -->
  <div class="relative max-w-3xl mx-auto">
    <!-- Heading -->
    <h1
      class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight mb-6 leading-tight"
    >
      <span
        class="block text-transparent bg-clip-text bg-gradient-to-r from-cyan-600 to-blue-800
               dark:from-cyan-400 dark:to-blue-500"
      >
        Selamat Datang di Platform Artikel Modern
      </span>
      <span
        class="block text-cyan-500 dark:text-cyan-400 drop-shadow-md"
      >
        Tikeli
      </span>
    </h1>

    <!-- Subtitle -->
    <p
      class="text-blue-700 dark:text-gray-300 text-lg md:text-xl leading-relaxed mb-8"
    >
      Ruang inspirasi, pengetahuan, dan informasi terpercaya dari para penulis terbaik kami.
    </p>

    <!-- Call to Action -->
    <div class="flex justify-center gap-4">
      <a
        href="#top-articles"
        class="inline-block bg-cyan-600 text-white font-semibold px-8 py-3 rounded-xl shadow-md
               hover:bg-cyan-500 dark:bg-cyan-500 dark:hover:bg-cyan-400 transition-all duration-300"
      >
        Jelajahi Artikel
      </a>
      <a
        href="/about"
        class="inline-block border border-cyan-600 text-cyan-700 font-semibold px-8 py-3 rounded-xl
               hover:bg-cyan-50
               dark:border-cyan-400 dark:text-cyan-300 dark:hover:bg-gray-800
               transition-all duration-300"
      >
        Tentang Kami
      </a>
    </div>
  </div>
</section>


<!-- Pengguna Berdasarkan Role -->
<section class="mb-8">
  <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
    <h3 class="text-lg font-bold text-blue-700 dark:text-cyan-400 text-center mb-4">
      Pengguna Terbaru
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
      <Link
        v-for="user in props.latestUsers"
        :key="user.id"
        :href="route('guest.profile', user.id)"
        class="block bg-gradient-to-br from-white to-blue-50 dark:from-gray-800 dark:to-gray-700
               border border-blue-100 dark:border-gray-600
               rounded-lg p-4 shadow hover:shadow-md transition-all text-center hover:scale-[1.01]"
      >
        <!-- Foto Profil -->
        <div class="w-16 h-16 mx-auto mb-2 relative">
          <div class="w-16 h-16 rounded-full border-2 border-blue-300 dark:border-cyan-500 shadow-inner overflow-hidden">
            <img
              :src="user.profile_photo_path
                ? `/storage/${user.profile_photo_path}`
                : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random`"
              alt="Foto Profil"
              class="w-full h-full object-cover"
            />
          </div>
        </div>

        <!-- Nama -->
        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">
          {{ user.name }}
        </p>

        <!-- Badge Role Kotak -->
        <span
          class="inline-block mt-1 px-2 py-1 text-[11px] font-semibold rounded-md"
          :class="{
            'bg-green-100 text-green-800 border border-green-300 dark:bg-green-900 dark:text-green-300 dark:border-green-700': user.role === 'admin',
            'bg-green-50 text-green-600 border border-green-200 dark:bg-green-800 dark:text-green-200 dark:border-green-700': user.role === 'operator',
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

<!-- Top 3 Artikel Terhits -->
<!-- Top 3 Artikel Terhits -->
<div class="mb-8">
  <h3
  id="top-articles"
  class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center gap-2"
>
  Artikel Terhits
  <span
    class="flex-1 h-[2px] bg-gradient-to-r from-pink-500 via-purple-500 to-transparent rounded-full"
  ></span>
</h3>


  <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
    <div
      v-for="article in props.topArticles"
      :key="article.id"
      class="rounded-xl p-[2px] bg-gradient-to-r from-emerald-500 via-teal-500 to-blue-500
             hover:from-emerald-600 hover:via-teal-600 hover:to-blue-600
             transition duration-300 cursor-pointer hover:scale-[1.01]"
      @click="router.visit(`/articles/${article.id}`)"
    >
      <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg flex flex-col overflow-hidden h-full">

        <!-- Gambar cover -->
        <div class="relative w-full h-48">
          <img
            :src="article.cover ?? 'https://via.placeholder.com/600x300?text=No+Cover'"
            alt="cover"
            class="w-full h-full object-cover"
          />
          <div
            v-if="article.category"
            class="absolute top-2 right-2 bg-gradient-to-r from-emerald-600 to-blue-600 text-white
                   text-[10px] px-2 py-1 rounded-md shadow-sm uppercase font-semibold"
          >
            {{ article.category }}
          </div>
        </div>

        <!-- Konten -->
        <div class="p-5 flex flex-col flex-grow">
          <span class="text-xs text-teal-500 uppercase font-semibold mb-1">Terhits</span>
          <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2 line-clamp-2">
            {{ article.title ?? 'Judul tidak tersedia' }}
          </h4>

          <div class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3 min-h-[65px] flex-grow"
               v-html="article.summary ?? 'Tidak ada ringkasan'">
          </div>

          <!-- Penulis, hits & waktu -->
          <div class="text-xs text-gray-400 dark:text-gray-500 mt-4 flex justify-between items-center">
            <div>
              <span>{{ article.author?.name ?? 'Anonim' }}</span> •
              <span>
                {{
                  article.updated_at
                    ? dayjs(article.updated_at).from(now.value)
                    : article.created_at
                    ? dayjs(article.created_at).from(now.value)
                    : 'Waktu tidak diketahui'
                }}
              </span>
            </div>

            <!-- Hits -->
            <span class="inline-flex items-center px-3 py-1 rounded-md text-white font-semibold
                         bg-gradient-to-r from-blue-500 via-teal-400 to-green-400
                         hover:scale-105 transform transition duration-300
                         shadow-md hover:shadow-[0_0_15px_rgba(0,255,255,0.7)]">
              <!-- Emoji Api -->
              <span class="mr-1 animate-pulse">🔥</span>
              {{ article.hits ?? 0 }} hits
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


   <h3
  id="top-articles"
  class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center gap-2"
>
  Artikel Terbaru
  <span
    class="flex-1 h-[2px] bg-gradient-to-r from-pink-500 via-purple-500 to-transparent rounded-full"
  ></span>
</h3>


      <!-- Filter & Search Stylish -->
<div class="flex flex-col md:flex-row md:items-center gap-4 mb-6">
  <!-- Input Search -->
  <div class="relative w-full md:w-1/2">
    <input
      v-model="search"
      type="text"
      placeholder="Cari artikel..."
      class="w-full pl-10 pr-4 py-2 border rounded-md shadow-sm
             focus:outline-none focus:ring-2 focus:ring-blue-300"
    />
    <!-- Icon Search -->
    <svg xmlns="http://www.w3.org/2000/svg"
         class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-500"
         fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.65 6.15a7.5 7.5 0 016.5 10.5z" />
    </svg>
  </div>

<!-- Dropdown kategori -->
<div class="relative w-full md:w-1/4 dropdown-wrapper" @click="toggleDropdown">
  <button
    class="w-full px-4 py-2 border rounded-md shadow-sm bg-white text-left
           flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-blue-300"
  >
    <span class="bg-gradient-to-r from-blue-600 via-sky-500 to-cyan-400 bg-clip-text text-transparent font-medium flex items-center gap-2">
      <!-- Icon Category -->
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
      {{ category || 'Semua Kategori' }}
    </span>
    <svg class="w-4 h-4 ml-2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
    </svg>
  </button>

  <!-- Animasi slide + fade -->
  <transition name="slide-fade">
    <ul
      v-if="dropdownOpen"
      class="absolute z-10 mt-2 w-full bg-white border rounded-xl shadow-lg overflow-hidden divide-y divide-gray-100"
    >
      <!-- Semua kategori -->
      <li
        @click.stop="selectCategory('')"
        class="px-4 py-2 flex items-center gap-2 cursor-pointer
               hover:bg-gradient-to-r hover:from-blue-50 hover:via-sky-50 hover:to-cyan-50
               hover:font-semibold transition-all duration-200"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
        </svg>
        Semua Kategori
      </li>

      <!-- List kategori -->
      <li
        v-for="cat in uniqueCategories"
        :key="cat"
        @click.stop="selectCategory(cat)"
        class="px-4 py-2 flex items-center gap-2 cursor-pointer
               hover:bg-gradient-to-r hover:from-blue-50 hover:via-sky-50 hover:to-cyan-50
               hover:font-semibold transition-all duration-200"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        {{ cat }}
      </li>
    </ul>
  </transition>
</div>



  <!-- Tombol Cari -->
  <button
    @click="submitFilter"
    class="flex items-center gap-2 px-5 py-2.5 rounded-md text-white font-medium
           shadow-md transition-all duration-200
           bg-gradient-to-r from-blue-600 via-sky-500 to-cyan-400
           hover:from-blue-700 hover:via-sky-600 hover:to-cyan-500
           active:scale-[0.97] hover:shadow-lg"
  >
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.65 6.15a7.5 7.5 0 016.5 10.5z" />
    </svg>
    Cari
  </button>
</div>


     <!-- Grid Artikel -->
<div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
  <div
    v-for="article in props.articles.data"
    :key="article.id"
    class="rounded-xl p-[1px] bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500
           hover:from-blue-600 hover:via-indigo-600 hover:to-purple-600
           transition duration-300 cursor-pointer"
    @click="router.visit(`/articles/${article.id}`)"
  >
    <div
      class="bg-white dark:bg-gray-900 rounded-xl shadow-sm hover:shadow-lg
             transition duration-300 flex flex-col overflow-hidden h-full"
    >

      <!-- Gambar cover -->
      <div class="relative w-full h-48">
        <img
          :src="article.cover ?? 'https://via.placeholder.com/600x300?text=No+Cover'"
          alt="cover"
          class="w-full h-full object-cover"
        />
        <div
          v-if="article.category"
          class="absolute top-2 right-2 bg-gradient-to-r from-blue-600 to-purple-600
                 text-white text-[10px] px-2 py-0.5 rounded-full shadow-sm uppercase"
        >
          {{ article.category }}
        </div>
      </div>

      <!-- Konten -->
      <div class="p-5 flex flex-col flex-grow">
        <span class="text-xs text-blue-500 uppercase font-semibold mb-1">Artikel</span>
        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">
          {{ article.title }}
        </h4>

        <!-- Fallback summary -->
        <div
          class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3 min-h-[60px] flex-grow"
          v-html="article.summary || 'Tidak ada ringkasan'"
        ></div>

        <!-- Penulis & waktu -->
        <div class="text-xs text-gray-400 dark:text-gray-500 mt-4">
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
</div>


     <!-- Pagination -->
<div
  class="flex justify-between items-center mt-10"
  v-if="props.articles.total > props.articles.per_page"
>
  <!-- Tombol Sebelumnya -->
  <button
    @click="goToPage(props.articles.prev_page_url)"
    :disabled="!props.articles.prev_page_url"
    class="flex items-center gap-2 px-5 py-2.5 rounded-md text-white font-medium
           shadow-md transition-all duration-200
           bg-gradient-to-r from-blue-600 via-sky-500 to-cyan-400
           hover:from-blue-700 hover:via-sky-600 hover:to-cyan-500
           active:scale-[0.97] hover:shadow-lg
           disabled:opacity-40 disabled:cursor-not-allowed"
  >
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
    </svg>
    Sebelumnya
  </button>

  <!-- Info Halaman -->
  <span class="text-gray-600 font-medium">
    Halaman {{ props.articles.current_page }} dari {{ props.articles.last_page }}
  </span>

  <!-- Tombol Selanjutnya -->
  <button
    @click="goToPage(props.articles.next_page_url)"
    :disabled="!props.articles.next_page_url"
    class="flex items-center gap-2 px-5 py-2.5 rounded-md text-white font-medium
           shadow-md transition-all duration-200
           bg-gradient-to-r from-blue-600 via-sky-500 to-cyan-400
           hover:from-blue-700 hover:via-sky-600 hover:to-cyan-500
           active:scale-[0.97] hover:shadow-lg
           disabled:opacity-40 disabled:cursor-not-allowed"
  >
    Selanjutnya
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
    </svg>
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
