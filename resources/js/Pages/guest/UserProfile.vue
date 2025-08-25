<!-- resources/js/Pages/guest/UserProfile.vue -->
<script setup>
import { defineProps, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import 'dayjs/locale/id'

dayjs.locale('id')
dayjs.extend(relativeTime)

const props = defineProps({
  user: Object,
  articles: Array,
  stats: Object
})

const now = ref(new Date())
</script>

<template>
  <PublicLayout>
<!-- Background -->
<section class="relative w-full h-48 bg-gray-200 overflow-hidden">
  <img
    v-if="user.background_photo_path"
    :src="`/storage/${user.background_photo_path}`"
    alt="Background"
    class="absolute inset-0 w-full h-full object-cover"
  />
</section>

<!-- Profil -->
<!-- Profil -->
<div class="max-w-4xl mx-auto px-4 relative">
  <div
    class="bg-white dark:bg-gray-900 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700 -mt-16"
  >
    <div class="flex items-start justify-between gap-6">
      <!-- Kiri: Info utama -->
      <div class="flex items-start gap-6">
        <!-- Foto profil -->
        <div
          class="w-28 h-28 border-4 border-white dark:border-gray-800 rounded-full overflow-hidden shadow -mt-16"
        >
          <img
            :src="
              user.profile_photo_path
                ? `/storage/${user.profile_photo_path}`
                : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random`
            "
            alt="Foto Profil"
            class="w-full h-full object-cover"
          />
        </div>

        <!-- Info teks -->
        <div class="flex-1">
          <!-- Sapaan -->
          <p class="text-sm text-gray-500 dark:text-gray-400">
            👋 Hi! saya <span class="font-semibold">{{ user.name }}</span>
          </p>

          <!-- Nama -->
          <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
            {{ user.name }}
          </h2>

          <!-- Role -->
          <span
            class="inline-block mt-1 px-3 py-0.5 text-xs font-semibold rounded-full
                   bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200"
          >
            {{ user.role }}
          </span>

          <!-- Bio -->
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
            {{ user.bio ?? 'Belum ada bio.' }}
          </p>

          <!-- Tanggal -->
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
            📅 Bergabung sejak {{ dayjs(user.created_at).format('DD MMMM YYYY') }}
          </p>
        </div>
      </div>

<!-- Kanan: Statistik mini -->
<div class="grid grid-cols-5 gap-2 text-center text-xs min-w-[220px]">
  <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-2 shadow-sm">
    <p class="font-bold text-gray-900 dark:text-gray-100">
      {{ stats?.hits ?? 'Tidak diketahui' }}
    </p>
    <p class="text-[10px] text-gray-500">Hits</p>
  </div>

  <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-2 shadow-sm">
    <p class="font-bold text-gray-900 dark:text-gray-100">
      {{ stats?.views ?? 'Tidak diketahui' }}
    </p>
    <p class="text-[10px] text-gray-500">Views</p>
  </div>

  <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-2 shadow-sm">
    <p class="font-bold text-gray-900 dark:text-gray-100">
      {{ stats?.likes ?? 'Tidak diketahui' }}
    </p>
    <p class="text-[10px] text-gray-500">Likes</p>
  </div>

  <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-2 shadow-sm">
    <p class="font-bold text-gray-900 dark:text-gray-100">
      {{ stats?.comments ?? '0' }}
    </p>
    <p class="text-[10px] text-gray-500">Comments</p>
  </div>

  <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-2 shadow-sm">
    <p class="font-bold text-gray-900 dark:text-gray-100">
      {{ stats?.approved_articles ?? '0' }}
    </p>
    <p class="text-[10px] text-gray-500">Artikel</p>
  </div>
</div>


</div>
</div>
</div>

    <!-- Artikel User -->
    <div class="max-w-6xl mx-auto mt-12 px-4">
      <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-6">
        Artikel oleh {{ user.name }}
      </h3>

      <div v-if="articles.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="article in articles"
          :key="article.id"
          class="rounded-xl p-[1px] bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500
                 hover:from-blue-600 hover:via-indigo-600 hover:to-purple-600
                 transition duration-300 cursor-pointer"
        >
          <Link
            :href="`/articles/${article.id}`"
            class="bg-white dark:bg-gray-900 rounded-xl shadow-sm hover:shadow-lg
                   transition duration-300 flex flex-col overflow-hidden h-full"
          >
            <!-- Gambar cover -->
            <div class="relative w-full h-48">
              <img
                :src="article.cover ? `/storage/${article.cover}` : 'https://via.placeholder.com/600x300?text=No+Cover'"
                alt="Cover"
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

              <!-- Summary -->
              <div
                class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3 min-h-[60px] flex-grow"
                v-html="article.summary || 'Tidak ada ringkasan'"
              ></div>

            <!-- Penulis, waktu, hits & likes -->
<div class="flex items-center justify-between text-xs mt-4">
  <div class="text-gray-400 dark:text-gray-500">
    {{ user.name }} •
    {{
      article.updated_at
        ? dayjs(article.updated_at).from(now)
        : dayjs(article.created_at).from(now)
    }}
  </div>
  <div class="flex items-center gap-2">
    <span
      class="inline-flex items-center bg-yellow-100 dark:bg-yellow-900
             text-yellow-700 dark:text-yellow-200 text-xs font-semibold
             px-2 py-0.5 rounded-full"
    >
      🔄 {{ article.hits ?? 0 }}
    </span>
    <span
      class="inline-flex items-center bg-red-100 dark:bg-red-900
             text-red-700 dark:text-red-200 text-xs font-semibold
             px-2 py-0.5 rounded-full"
    >
      ❤️ {{ article.likes_count ?? 0 }}
    </span>
  </div>
</div>

            </div>
          </Link>
        </div>
      </div>

      <div v-else class="text-center text-gray-500 dark:text-gray-400 mt-10">
        Pengguna ini belum memiliki artikel yang disetujui.
      </div>
    </div>

    <!-- Spacer -->
    <div class="h-10"></div>
  </PublicLayout>
</template>
