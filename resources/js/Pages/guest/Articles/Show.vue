<script setup>
import { Head } from '@inertiajs/vue3'
import { defineProps } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
  article: Object,
  views: Number,
  from: String, // URL asal, dikirim lewat query ?from=...
})

const goBack = () => {
  if (props.from) {
    window.location.href = props.from
  } else if (window.history.length > 1) {
    window.history.back()
  } else {
    window.location.href = '/welcome'
  }
}
</script>

<template>
  <PublicLayout>
    <Head :title="article.title" />

    <main class="flex-grow max-w-4xl mx-auto px-4 py-12 space-y-6">
      <!-- Ringkasan Artikel -->
      <div class="bg-white rounded-lg shadow-md p-6 border">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Ringkasan Artikel</h2>
        <p
          class="text-gray-600 text-sm leading-relaxed"
          v-html="article.summary ?? 'Artikel ini membahas topik yang menarik dan informatif secara mendalam. Baca selengkapnya di bawah.'"
        ></p>
      </div>

      <!-- Artikel -->
      <div class="bg-white rounded-lg shadow-md border p-6 space-y-6">
        <!-- Judul & Info -->
        <div>
          <h1 class="text-4xl font-bold text-gray-800 leading-tight mb-1">
            {{ article.title }}
          </h1>

          <!-- Kategori + Views + Hits -->
          <div class="flex flex-wrap items-center gap-2 mb-2">
            <div
              v-if="article.category"
              class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full"
            >
              {{ article.category }}
            </div>
            <div
              class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full"
            >
              👁️ {{ views ?? 0 }} Views
            </div>
            <div
              class="inline-block bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full"
            >
              🔄 {{ article.hits ?? 0 }} Hits
            </div>
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

      <!-- Konten Artikel -->
<div class="max-w-none text-gray-900 prose
            [&_h1]:text-4xl [&_h2]:text-3xl [&_h3]:text-2xl [&_h4]:text-xl [&_h5]:text-lg [&_h6]:text-base
            [&_ul]:list-disc [&_ul]:pl-6
            [&_ol]:list-decimal [&_ol]:pl-6
            [&_pre]:bg-gray-900 [&_pre]:text-white [&_pre]:p-4 [&_pre]:rounded [&_pre]:overflow-x-auto [&_pre]:font-mono [&_pre]:text-sm [&_pre]:my-2
            [&_code]:text-red-500
            [&_p]:text-left
            [&_p.ql-align-center]:text-center
            [&_p.ql-align-right]:text-right
            [&_blockquote]:border-l-4 [&_blockquote]:border-gray-300 [&_blockquote]:pl-4 [&_blockquote]:italic">
  <div v-html="article.content"></div>
</div>



        <!-- Tentang Penulis -->
        <div
          class="rounded-lg p-4 shadow-lg flex items-center gap-4
                 bg-gradient-to-r from-purple-500 via-pink-500 to-blue-500"
        >
          <div class="bg-white/90 backdrop-blur-sm flex items-center gap-4 p-3 rounded-lg w-full">
            <!-- Foto Profil -->
            <img
              :src="article.author.profile_photo_path
                ? `/storage/${article.author.profile_photo_path}`
                : `https://ui-avatars.com/api/?name=${encodeURIComponent(article.author.name)}`"
              alt="Foto Profil"
              class="w-14 h-14 rounded-full object-cover border-2 border-white shadow"
            />

            <!-- Info Penulis -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2">
                <p class="text-base font-bold text-gray-800 truncate">{{ article.author.name }}</p>
                <span
                  class="text-xs font-medium px-2 py-0.5 rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': article.author.role === 'admin',
                    'bg-green-50 text-green-700': article.author.role === 'operator',
                  }"
                >
                  {{ article.author.role }}
                </span>
              </div>
              <p v-if="article.author.bio" class="text-gray-600 text-xs mt-1 line-clamp-2">
                {{ article.author.bio }}
              </p>
            </div>

            <!-- Tombol Profil -->
            <a
              :href="`/users/${article.author.id}`"
              class="shrink-0 flex items-center gap-1 text-xs px-3 py-1.5 rounded-full text-white font-medium transition
                     bg-gradient-to-r from-purple-600 via-pink-500 to-indigo-500 hover:scale-105 shadow-md"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M5.121 17.804A9.969 9.969 0 0112 15c2.21 0 4.255.72 5.879 1.929M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Profil
            </a>
          </div>
        </div>

        <!-- Tombol Kembali -->
        <div class="pt-6">
          <button
            @click="goBack"
            class="flex items-center gap-2 bg-gradient-to-r from-blue-600 via-sky-500 to-cyan-400
                   text-white px-5 py-2 rounded-md font-medium shadow-md hover:shadow-lg
                   hover:scale-[1.02] active:scale-[0.98] transition-transform duration-150"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
          </button>
        </div>
      </div>
    </main>
  </PublicLayout>
</template>
