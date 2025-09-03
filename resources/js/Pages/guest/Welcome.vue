<script setup>
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import 'dayjs/locale/id'
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { CheckCircleIcon } from '@heroicons/vue/24/solid'
import { CheckCircle } from 'lucide-vue-next'

dayjs.locale('id')
dayjs.extend(relativeTime)

const props = defineProps({
  articles: Object,
  latestUsers: Array,
    topArticles: Array,
  filters: Object, // biar nilai search/category tetap setelah reload
  hits: Number, // total hits semua artikel
  likes: Number, // total likes semua artikel
    comments: Number, // total komentar semua artikel
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
    class="absolute top-[-4rem] left-[-4rem] w-96 h-96 bg-cyan-200
           dark:bg-cyan-900
           rounded-full mix-blend-multiply filter blur-3xl opacity-30
           animate-float-slow"
  ></div>
  <div
    class="absolute bottom-[-6rem] right-[-6rem] w-[28rem] h-[28rem] bg-blue-300
           dark:bg-blue-900
           rounded-full mix-blend-multiply filter blur-3xl opacity-25
           animate-float-slower"
  ></div>

  <!-- Content -->
  <div class="relative max-w-3xl mx-auto">
    <!-- Heading -->
    <h1
      class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight mb-6 leading-tight"
    >
      <span
        class="block text-transparent bg-clip-text bg-gradient-to-r from-cyan-600 to-blue-800
               dark:from-cyan-400 dark:to-blue-500 bg-[length:200%_200%] animate-gradient-flow"
      >
        Selamat Datang di Platform Artikel Modern
      </span>
      <span
        class="block text-cyan-600 dark:text-cyan-400 drop-shadow-md"
      >
        Tikeli
      </span>
    </h1>

    <!-- Subtitle -->
    <p
      class="text-blue-700 dark:text-gray-300 text-lg md:text-xl leading-relaxed mb-10"
    >
      Ruang inspirasi, pengetahuan, dan informasi terpercaya dari para penulis terbaik kami.
    </p>

    <!-- Call to Action -->
    <div class="flex justify-center gap-4">
      <a
        href="#top-articles"
        class="inline-block bg-cyan-600 text-white font-semibold px-8 py-3 rounded-xl shadow-lg
               hover:bg-cyan-500 dark:bg-cyan-500 dark:hover:bg-cyan-400
               hover:shadow-[0_0_20px_rgba(34,211,238,0.6)] transition-all duration-300 hover:scale-[1.05]"
      >
        Jelajahi Artikel
      </a>
      <a
        href="/about"
        class="inline-block border border-cyan-600 text-cyan-700 font-semibold px-8 py-3 rounded-xl
               hover:bg-cyan-50
               dark:border-cyan-400 dark:text-cyan-300 dark:hover:bg-gray-800
               transition-all duration-300 hover:scale-[1.03]"
      >
        Tentang Kami
      </a>
    </div>
  </div>
</section>


<!-- Pengguna Terbaru -->
<section class="mb-8">
  <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg p-6">
    <!-- Judul -->
    <div class="flex items-center justify-center mb-10">
      <span class="h-0.5 w-12 bg-gradient-to-r from-transparent via-blue-400 to-transparent"></span>
      <span class="mx-4 text-xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-500 via-cyan-400 to-teal-500">
        Pengguna Terbaru
      </span>
      <span class="h-0.5 w-12 bg-gradient-to-r from-transparent via-blue-400 to-transparent"></span>
    </div>

    <!-- Grid Users -->
    <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <Link
        v-for="user in props.latestUsers"
        :key="user.id"
        :href="route('guest.profile', user.id)"
        class="group block rounded-2xl p-[2px] transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl"
        :class="user.role === 'admin'
          ? 'bg-gradient-to-r from-purple-500 via-pink-500 to-red-500'
          : 'bg-gradient-to-r from-blue-400 via-cyan-400 to-teal-500'"
      >
        <!-- Inner Card -->
        <div
          class="rounded-2xl bg-white dark:bg-gray-800 p-5 h-full shadow-md flex flex-col transition-all duration-300 group-hover:bg-gradient-to-br group-hover:from-gray-50 dark:group-hover:from-gray-700"
        >
          <!-- Header: Foto + Nama + Bio -->
          <div class="flex items-start gap-4">
            <!-- Foto Profil -->
            <div
              class="w-16 h-16 flex-shrink-0 rounded-full ring-2 ring-offset-2 overflow-hidden bg-gradient-to-tr from-blue-400 to-cyan-500 p-[2px]"
            >
              <img
                :src="user.profile_photo_path
                  ? `/storage/${user.profile_photo_path}`
                  : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random`"
                alt="Foto Profil"
                class="w-full h-full rounded-full object-cover"
              />
            </div>

            <!-- Nama & Bio -->
            <div class="flex-1 min-w-0">
              <!-- Nama + Verified -->
              <h4
                class="flex items-center gap-1 text-sm font-semibold text-gray-800 dark:text-gray-200 truncate"
              >
                <span class="group-hover:text-blue-500 dark:group-hover:text-cyan-400 transition">
                  {{ user.name.split(' ').slice(0, 2).join(' ') }}
                </span>
                <CheckCircle
                  v-if="user.trusted_writer"
                  class="w-4 h-4 text-white bg-green-500 rounded-full p-[1px] flex-shrink-0"
                  :stroke-width="2"
                />
              </h4>

              <!-- Bio -->
              <p
                class="mt-1 text-[11px] text-gray-600 dark:text-gray-400 line-clamp-3 leading-snug italic"
              >
                {{ user.bio || 'Belum ada bio' }}
              </p>
            </div>
          </div>

          <!-- Footer: Badges + Tanggal -->
          <div class="mt-5 flex flex-col flex-1 justify-end">
            <!-- Badges -->
            <div class="flex flex-wrap gap-2 mb-2">
              <!-- Role -->
              <span
                class="inline-block px-2 py-0.5 text-[11px] font-medium rounded-lg"
                :class="{
                  'bg-green-100 text-green-800 border border-green-300 dark:bg-green-900 dark:text-green-300 dark:border-green-700': user.role === 'admin',
                  'bg-blue-100 text-blue-700 border border-blue-300 dark:bg-blue-900 dark:text-blue-300 dark:border-blue-700': user.role === 'operator',
                }"
              >
                {{ user.role }}
              </span>

              <!-- Operator stats -->
              <template v-if="user.role === 'operator'">
                <span
                  class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-lg
                             bg-pink-100 text-pink-700 border border-pink-300
                             dark:bg-pink-900 dark:text-pink-300 dark:border-pink-700"
                >
                  ❤️ {{ user.total_likes || 0 }}
                </span>
                <span
                  class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-lg
                             bg-yellow-100 text-yellow-700 border border-yellow-300
                             dark:bg-yellow-900 dark:text-yellow-300 dark:border-yellow-700"
                >
                  🔥 {{ user.total_hits || 0 }}
                </span>
              </template>

              <!-- Admin badge -->
              <template v-else-if="user.role === 'admin'">
                <span
                  class="inline-flex items-center px-2 py-0.5 text-[11px] font-semibold rounded-lg
                         bg-gradient-to-r from-yellow-200 via-yellow-400 to-yellow-500 text-yellow-900
                         border border-yellow-300
                         dark:bg-gradient-to-r dark:from-yellow-600 dark:via-yellow-700 dark:to-yellow-800
                         dark:text-yellow-100 dark:border-yellow-700 shadow-md"
                >
                  🔑 Full Access
                </span>
              </template>
            </div>

            <!-- Tanggal Bergabung -->
            <p
              class="text-[11px] text-gray-400 dark:text-gray-500 italic"
            >
              Bergabung {{ dayjs(user.created_at).fromNow() }}
            </p>
          </div>
        </div>
      </Link>
    </div>
  </div>
</section>


    <!-- Artikel -->
    <main class="flex-grow max-w-7xl mx-auto px-4 py-10">

<!-- Top 3 Artikel Terhits -->
<div class="mb-12">
  <h3
    id="top-articles"
    class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center gap-2"
  >
    <span
      class="bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 text-transparent bg-clip-text"
    >
      Artikel Terhits
    </span>
    <span
      class="flex-1 h-[2px] bg-gradient-to-r from-pink-500 via-purple-500 to-transparent rounded-full"
    ></span>
  </h3>

  <div class="grid gap-8 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
    <div
      v-for="article in props.topArticles"
      :key="article.id"
      @click="router.visit(`/articles/${article.id}`)"
      class="group cursor-pointer"
    >
      <!-- Bingkai gradasi -->
      <div
        class="p-[2px] rounded-2xl bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 shadow-lg hover:shadow-2xl transition transform hover:-translate-y-1"
      >
        <!-- Isi card -->
        <div class="relative rounded-2xl overflow-hidden bg-white dark:bg-gray-900">
          <!-- Cover -->
          <div class="relative w-full h-56">
            <img
              :src="article.cover ?? 'https://via.placeholder.com/600x300?text=No+Cover'"
              alt="cover"
              class="w-full h-full object-cover"
            />

            <!-- Overlay gradient -->
            <div
              class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"
            ></div>

            <!-- Badge kategori -->
            <div
              class="absolute top-3 left-3 text-white text-xs px-3 py-1 rounded-full font-semibold shadow-lg backdrop-blur-sm"
              :class="article.category
                        ? 'bg-gradient-to-r from-emerald-500 to-blue-500'
                        : 'bg-gradient-to-r from-pink-600 to-purple-600'"
            >
              {{ article.category ?? 'UNKNOWN' }}
            </div>
          </div>

          <!-- Konten -->
          <div class="absolute bottom-0 left-0 right-0 p-5 z-10">
            <!-- Judul nyala -->
            <h4
              class="text-xl font-extrabold mb-2 leading-snug
                     text-white drop-shadow-lg group-hover:text-pink-300 line-clamp-2"
            >
              {{ article.title ?? 'Judul tidak tersedia' }}
            </h4>

            <!-- Ringkasan -->
            <p
              class="text-sm text-gray-200 line-clamp-2 mb-3"
              v-html="article.summary ?? 'Tidak ada ringkasan'"
            ></p>

            <!-- Info penulis & waktu -->
            <div class="flex items-center text-xs text-gray-300 mb-3">
              <span class="flex items-center gap-1">
                {{ article.author?.name ?? 'Anonim' }}
                <CheckCircle
                  v-if="article.trusted_writer"
                  class="w-3 h-3 text-white bg-green-500 rounded-full p-[1px]"
                  :stroke-width="2"
                />
              </span>
              <span class="mx-1">•</span>
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

            <!-- Stats -->
            <div class="flex gap-2">
              <span
                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs text-white font-semibold
                       bg-gradient-to-r from-blue-500 to-teal-400 shadow-md"
              >
                🔥 {{ article.hits ?? 0 }}
              </span>
              <span
                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs text-white font-semibold
                       bg-gradient-to-r from-pink-500 to-orange-400 shadow-md"
              >
                ❤️ {{ article.likes ?? 0 }}
              </span>
              <span
                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs text-white font-semibold
                       bg-gradient-to-r from-purple-500 to-fuchsia-500 shadow-md"
              >
                💬 {{ article.comments_count ?? 0 }}
              </span>
            </div>
          </div>
        </div>
      </div>
      <!-- End bingkai -->
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
           bg-white text-gray-800
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
    <span class="text-gray-800 font-medium flex items-center gap-2">
      <!-- Icon Category -->
      <svg xmlns="http://www.w3.org/2000/svg"
           class="w-4 h-4 text-gray-600"
           fill="none"
           viewBox="0 0 24 24"
           stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
      {{ category || 'Semua Kategori' }}
    </span>

    <!-- Panah -->
    <svg class="w-4 h-4 ml-2 text-gray-500"
         fill="none" stroke="currentColor" stroke-width="2"
         viewBox="0 0 24 24">
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
        class="px-4 py-2 flex items-center gap-2 cursor-pointer text-gray-800
               hover:bg-gradient-to-r hover:from-blue-50 hover:via-sky-50 hover:to-cyan-50
               hover:font-semibold transition-all duration-200"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
        </svg>
        Semua Kategori
      </li>

      <!-- List kategori -->
      <li
        v-for="cat in uniqueCategories"
        :key="cat"
        @click.stop="selectCategory(cat)"
        class="px-4 py-2 flex items-center gap-2 cursor-pointer text-gray-800
               hover:bg-gradient-to-r hover:from-blue-50 hover:via-sky-50 hover:to-cyan-50
               hover:font-semibold transition-all duration-200"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
  <template v-for="(article, index) in props.articles.data" :key="article.id">
    <!-- Card Artikel -->
    <div
      class="rounded-xl p-[1px] bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500
             hover:from-blue-600 hover:via-indigo-600 hover:to-purple-600
             transition duration-300 cursor-pointer group"
      @click="router.visit(`/articles/${article.id}`)"
    >
      <div
        class="bg-white dark:bg-gray-900 rounded-xl shadow-sm group-hover:shadow-md
               transition duration-300 flex flex-col overflow-hidden h-full"
      >
        <!-- Gambar cover -->
        <div class="relative w-full h-48 overflow-hidden">
          <img
            :src="article.cover ?? 'https://via.placeholder.com/600x300?text=No+Cover'"
            alt="cover"
            class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
          />
          <div
            v-if="article.category"
            class="absolute top-2 right-2 bg-gradient-to-r from-indigo-600 to-purple-600
                   text-white text-[11px] px-2 py-0.5 rounded-full shadow-sm font-medium tracking-wide"
          >
            {{ article.category }}
          </div>
        </div>

        <!-- Konten -->
        <div class="p-5 flex flex-col flex-grow">
          <span class="text-[11px] text-indigo-600 uppercase font-semibold mb-1 tracking-wide">Artikel</span>
          <h4
            class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2 leading-snug
                   group-hover:text-indigo-600 dark:group-hover:text-indigo-400 line-clamp-2"
          >
            {{ article.title }}
          </h4>

          <!-- Ringkasan -->
          <div
            class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3 min-h-[60px] flex-grow"
            v-html="article.summary || 'Tidak ada ringkasan'"
          ></div>

          <!-- Footer -->
          <div class="text-xs text-gray-500 dark:text-gray-400 mt-4 flex justify-between items-center">
            <!-- Penulis & waktu -->
            <div class="flex items-center gap-1">
              <span class="flex items-center gap-1 font-medium text-gray-700 dark:text-gray-300">
                {{ article.author?.name ?? 'Anonim' }}
                <CheckCircle
                  v-if="article.trusted_writer"
                  class="w-3 h-3 text-white bg-green-500 rounded-full p-[1px]"
                  :stroke-width="2"
                />
              </span>
              <span>•</span>
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

            <!-- Statistik -->
            <div class="flex items-center gap-2">
              <span
                class="inline-flex items-center bg-yellow-50 dark:bg-yellow-900/40
                       text-yellow-700 dark:text-yellow-200 text-xs font-medium
                       px-2 py-0.5 rounded-full"
              >
                🔄 {{ article.hits ?? 0 }}
              </span>
              <span
                class="inline-flex items-center bg-red-50 dark:bg-red-900/40
                       text-red-700 dark:text-red-200 text-xs font-medium
                       px-2 py-0.5 rounded-full"
              >
                ❤️ {{ article.likes ?? 0 }}
              </span>
              <span
                class="inline-flex items-center bg-blue-50 dark:bg-blue-900/40
                       text-blue-700 dark:text-blue-200 text-xs font-medium
                       px-2 py-0.5 rounded-full"
              >
                💬 {{ article.comments_count ?? 0 }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Banner disisipkan setelah 2 artikel -->
<div
  v-if="index === 1"
  class="rounded-xl p-[1px] bg-gradient-to-r from-blue-500 to-indigo-500
         hover:from-blue-600 hover:to-indigo-600 transition duration-300 cursor-pointer group"
>
  <div
    class="bg-white dark:bg-gray-900 rounded-xl shadow-sm flex flex-col overflow-hidden h-full"
  >
    <!-- Banner isi -->
    <div class="relative w-full aspect-[4.5/4.7] overflow-hidden">
      <img
        src="Tikelibanner.png"
        alt="Tikeli Banner"
        class="w-full h-full object-cover"
      />
    </div>
  </div>
</div>

  </template>
</div>





<!-- Pagination + CTA -->
<div
  class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mt-10"
  v-if="props.articles.total > props.articles.per_page"
>
  <div class="flex flex-col md:flex-row justify-between items-center gap-6 mt-6 w-full">

  <!-- Bagian Pagination -->
  <div class="flex items-center gap-4 flex-1">
    <!-- Tombol Sebelumnya -->
    <button
      @click="goToPage(props.articles.prev_page_url)"
      :disabled="!props.articles.prev_page_url"
      class="flex-1 flex items-center justify-center gap-2 px-5 py-2.5 rounded-md text-white font-medium
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
    <span class="flex-1 text-center text-gray-600 font-medium whitespace-nowrap">
      Halaman {{ props.articles.current_page }} dari {{ props.articles.last_page }}
    </span>

    <!-- Tombol Selanjutnya -->
    <button
      @click="goToPage(props.articles.next_page_url)"
      :disabled="!props.articles.next_page_url"
      class="flex-1 flex items-center justify-center gap-2 px-5 py-2.5 rounded-md text-white font-medium
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

<!-- CTA -->
<div class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-indigo-700 to-blue-800
            text-white px-6 py-3 rounded-xl shadow-md text-center md:text-left md:ml-6">

  <!-- Pattern titik-titik halus -->
  <div class="absolute inset-0 opacity-15
              bg-[radial-gradient(circle_at_center,white_1px,transparent_1px)]
              [background-size:20px_20px]"></div>

  <!-- Overlay gelap tipis -->
  <div class="absolute inset-0 bg-black/10"></div>

  <div class="relative z-10">
    <h3 class="text-lg font-semibold mb-1">Gabung dengan Tikeli</h3>
    <p class="text-sm text-gray-100 mb-2">Bagikan ide hebatmu bersama komunitas.</p>
    <a href="/register"
       class="inline-block bg-yellow-400 hover:bg-yellow-500 text-gray-900 px-4 py-2
              rounded-lg font-semibold shadow-md transition transform hover:scale-[1.03] text-sm">
      Mulai Menulis
    </a>
  </div>
</div>

</div>
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

@keyframes gradient-x {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}
.animate-gradient-x {
  background-size: 200% 200%;
  animation: gradient-x 5s ease infinite;
}

@keyframes float-slow {
  0%, 100% { transform: translateY(0) scale(1); }
  50% { transform: translateY(-25px) scale(1.05); }
}
@keyframes float-slower {
  0%, 100% { transform: translateY(0) scale(1); }
  50% { transform: translateY(25px) scale(1.08); }
}
@keyframes gradient-flow {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
.animate-float-slow {
  animation: float-slow 12s ease-in-out infinite;
}
.animate-float-slower {
  animation: float-slower 16s ease-in-out infinite;
}
.animate-gradient-flow {
  animation: gradient-flow 6s linear infinite;
}
</style>
