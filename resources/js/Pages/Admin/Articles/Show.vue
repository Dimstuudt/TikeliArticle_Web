<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { defineProps } from 'vue'

const props = defineProps({
  article: Object,
})
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="article.title" />

    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Artikel</h2>
    </template>

    <div class="py-6">
      <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">{{ article.title }}</h1>

        <div v-if="article.cover_url" class="mb-4">
          <img :src="article.cover_url" alt="Cover" class="w-full max-h-80 object-cover rounded" />
        </div>

        <p class="text-sm text-gray-500 mb-4">Status: {{ article.status }}</p>

        <h2 class="font-semibold mb-2">Ringkasan:</h2>
        <p class="mb-4">{{ article.summary }}</p>

        <h2 class="font-semibold mb-2">Isi Artikel:</h2>
        <div class="prose max-w-none" v-html="article.content" />

        <p class="text-sm text-gray-400 mt-6">Dibuat pada: {{ new Date(article.created_at).toLocaleString() }}</p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
