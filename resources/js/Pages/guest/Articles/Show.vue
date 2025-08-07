<script setup>
import { Head } from '@inertiajs/vue3'
import { defineProps } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
  article: Object,
  from: String, // dikirim saat navigasi
})

const goBack = () => {
  if (window.history.state?.back) {
    window.history.back()
  } else if (props.from) {
    window.location.href = props.from
  } else {
    window.location.href = '/welcome'
  }
}
</script>

<template>
  <PublicLayout>
    <!-- Page Title -->
    <Head :title="article.title" />

    <!-- Main Content -->
    <main class="flex-grow max-w-4xl mx-auto px-4 py-12 space-y-6">
      <!-- Summary Card -->
      <div class="bg-white rounded-lg shadow-md p-6 border">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Ringkasan Artikel</h2>
        <p class="text-gray-600 text-sm leading-relaxed">
          {{ article.summary ?? 'Artikel ini membahas topik yang menarik dan informatif secara mendalam. Baca selengkapnya di bawah.' }}
        </p>
      </div>

      <!-- Article Card -->
      <div class="bg-white rounded-lg shadow-md border p-6 space-y-6">
        <!-- Judul & Info -->
        <div>
          <h1 class="text-4xl font-bold text-gray-800 leading-tight mb-1">
            {{ article.title }}
          </h1>

          <!-- Badge Kategori -->
          <div
            v-if="article.category"
            class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full mb-2"
          >
            {{ article.category }}
          </div>

          <p class="text-sm text-gray-500">
            Oleh <span class="font-medium">{{ article.author.name }}</span> • {{ article.created_at }}
          </p>
        </div>

        <hr class="border-t border-gray-200" />

        <!-- Cover -->
        <div v-if="article.cover">
          <img
            :src="article.cover"
            alt="Cover"
            class="w-full max-h-[400px] object-cover rounded-lg shadow mb-4"
          />
        </div>

        <!-- Konten -->
        <div class="prose prose-lg prose-blue max-w-none text-gray-800" v-html="article.content" />

        <!-- About the Author -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 shadow-md">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Tentang Penulis</h2>
          <div class="flex items-center gap-4">
            <!-- Foto profil -->
            <img
              :src="article.author.profile_photo_path
                ? `/storage/${article.author.profile_photo_path}`
                : `https://ui-avatars.com/api/?name=${encodeURIComponent(article.author.name)}`"
              alt="Foto Profil"
              class="w-16 h-16 rounded-full object-cover border-4 border-blue-200"
            />

            <!-- Info Penulis -->
            <div>
              <p class="text-lg font-bold text-gray-800">{{ article.author.name }}</p>
              <span
                class="inline-block mt-1 text-xs font-medium px-3 py-1 rounded-full"
                :class="{
                  'bg-green-100 text-green-800': article.author.role === 'admin',
                  'bg-green-50 text-green-700': article.author.role === 'operator',
                }"
              >
                {{ article.author.role }}
              </span>
            </div>
          </div>
        </div>

        <!-- Tombol Kembali -->
        <div class="pt-6">
          <button
            @click="goBack"
            class="inline-block bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition"
          >
            ← Kembali
          </button>
        </div>
      </div>
    </main>
  </PublicLayout>
</template>
