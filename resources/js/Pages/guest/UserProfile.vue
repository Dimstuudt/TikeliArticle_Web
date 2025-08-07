<!-- resources/js/Pages/guest/UserProfile.vue -->
<script setup>
import { defineProps } from 'vue'
import { Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
  user: Object,
  articles: Array,
})
</script>

<template>
  <PublicLayout>
    <!-- Background -->
    <section class="relative w-full h-44 bg-gray-200 overflow-hidden">
      <img
        v-if="user.background_photo_path"
        :src="`/storage/${user.background_photo_path}`"
        alt="Background"
        class="absolute inset-0 w-full h-full object-cover"
      />
    </section>

    <!-- Profil -->
    <div class="max-w-2xl mx-auto px-4 -mt-20 z-10 relative">
      <div class="bg-white rounded-lg shadow p-5 text-center">
        <!-- Foto profil -->
        <div class="relative w-24 h-24 mx-auto -mt-16 border-4 border-white rounded-full overflow-hidden shadow">
          <img
            :src="user.profile_photo_path
              ? `/storage/${user.profile_photo_path}`
              : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random`"
            alt="Foto Profil"
            class="w-full h-full object-cover"
          />
        </div>

        <!-- Nama -->
        <h2 class="text-lg font-semibold text-gray-800 mt-3">{{ user.name }}</h2>

        <!-- Role -->
        <span
          class="inline-block mt-1 px-3 py-0.5 text-xs font-medium rounded-full"
          :class="{
            'bg-green-100 text-green-800': user.role === 'admin',
            'bg-green-50 text-green-600': user.role === 'operator',
          }"
        >
          {{ user.role }}
        </span>

        <!-- Bio -->
        <p class="text-gray-600 mt-3 text-sm">{{ user.bio ?? 'Belum ada bio.' }}</p>
      </div>
    </div>

    <!-- Artikel User -->
    <div class="max-w-6xl mx-auto mt-12 px-4">
      <h3 class="text-xl font-semibold text-gray-800 mb-6">Artikel oleh {{ user.name }}</h3>

      <div v-if="articles.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <Link
          v-for="article in articles"
          :key="article.id"
          :href="`/articles/${article.id}`"
          class="bg-white rounded-lg shadow hover:shadow-md transition overflow-hidden block"
        >
          <img
            :src="article.cover ? `/storage/${article.cover}` : 'https://via.placeholder.com/600x300?text=No+Cover'"
            alt="Cover"
            class="w-full h-36 object-cover"
          />

          <div class="p-4 flex flex-col justify-between min-h-[140px]">
            <!-- Badge kategori -->
      <span
  v-if="article.category"
  class="inline-flex w-fit mb-2 text-xs font-medium bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full uppercase"
>
  {{ article.category }}
</span>



            <div>
              <h4 class="text-md font-bold text-gray-800 leading-snug mb-1">
                {{ article.title }}
              </h4>

              <!-- Tanggal -->
              <p class="text-gray-500 text-xs leading-normal">
                Diterbitkan: {{ new Date(article.created_at).toLocaleDateString('id-ID') }}
              </p>
            </div>
          </div>
        </Link>
      </div>

      <div v-else class="text-center text-gray-500 mt-10">
        Pengguna ini belum memiliki artikel yang disetujui.
      </div>
    </div>
<br>
<br>
    <!-- Spacer agar tidak mepet dengan footer -->
    <div class="h-10"></div>
  </PublicLayout>
</template>
