<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

defineProps({ articles: Array })

const form = useForm({})

const destroy = (id) => {
  if (confirm('Yakin ingin menghapus artikel ini?')) {
    form.delete(`/operator/articles/${id}`, {
      preserveScroll: true,
    })
  }
}

// Hapus semua HTML tag (biar konten bersih)
const stripHtml = (html) => {
  const div = document.createElement('div')
  div.innerHTML = html
  return div.textContent || div.innerText || ''
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Artikel Saya" />

    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 leading-tight">Artikel Saya</h2>
    </template>

    <div class="py-8">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 shadow rounded-md">
          <div v-if="articles.length === 0" class="text-gray-500">Belum ada artikel.</div>

          <div v-else class="grid md:grid-cols-2 gap-6">
            <div
              v-for="article in articles"
              :key="article.id"
              class="border border-gray-200 p-4 rounded-lg shadow-sm bg-white flex flex-col justify-between"
            >
              <div class="flex justify-between items-start mb-2">
                <h3 class="text-lg font-semibold text-gray-800">{{ article.title }}</h3>
                <span
                  class="px-2 py-1 text-xs font-medium rounded-full capitalize"
                  :class="{
                    'bg-yellow-100 text-yellow-800': article.status === 'pending',
                    'bg-green-100 text-green-800': article.status === 'approved',
                    'bg-red-100 text-red-800': article.status === 'rejected',
                    'bg-gray-100 text-gray-800': article.status === 'draft'
                  }"
                >
                  {{ article.status }}
                </span>
              </div>

              <!-- Konten singkat, dibatasi 2 baris -->
              <p
                class="text-sm text-gray-700 line-clamp-2 mb-3"
              >
                {{ stripHtml(article.summary) }}
              </p>

              <div
                v-if="['draft', 'rejected'].includes(article.status)"
                class="mt-auto flex space-x-2"
              >
                <Link
                  :href="`/operator/articles/${article.id}/edit`"
                  class="bg-blue-600 text-white text-xs px-4 py-1 rounded hover:bg-blue-700 transition"
                >
                  Edit
                </Link>
                <button
                  @click="destroy(article.id)"
                  class="bg-red-600 text-white text-xs px-4 py-1 rounded hover:bg-red-700 transition"
                >
                  Hapus
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
