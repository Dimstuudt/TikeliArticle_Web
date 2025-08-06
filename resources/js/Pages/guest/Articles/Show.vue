<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { defineProps } from 'vue'

const props = defineProps({
  article: Object,
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
          <h1 class="text-4xl font-bold text-gray-800 leading-tight mb-2">{{ article.title }}</h1>
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

        <!-- Tombol Kembali -->
        <div class="pt-6">
          <Link
            href="/welcome"
            class="inline-block bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition"
          >
            ← Kembali
          </Link>
        </div>
      </div>
    </main>

    <!-- Footer CTA -->
    <footer class="text-center py-6 bg-gray-100 mt-10">
      <p class="text-gray-600">
        Belum punya akun?
        <a href="/register" class="text-blue-600 hover:underline font-semibold">Daftar sekarang</a>
      </p>
    </footer>
  </div>
</template>
