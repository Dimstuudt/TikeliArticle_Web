<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

defineProps({
  articles: Array,
})

const form = useForm({})

const destroy = (id) => {
  if (confirm('Yakin ingin menghapus artikel ini?')) {
    form.delete(`/operator/articles/${id}`, {
      preserveScroll: true,
    })
  }
}

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
              class="border border-gray-200 p-4 rounded-lg shadow-sm bg-white flex flex-col space-y-4"
            >
              <!-- Bagian Cover -->
              <div>
                <h4 class="text-sm font-semibold text-gray-600 mb-1">Cover</h4>
                <img
                  v-if="article.cover_url"
                  :src="article.cover_url"
                  alt="Cover"
                  class="w-full h-48 object-cover rounded"
                />
                <p v-else class="text-sm text-gray-400 italic">Tidak ada cover</p>
              </div>

              <!-- Judul & Status -->
              <div>
                <h4 class="text-sm font-semibold text-gray-600 mb-1">Judul & Status</h4>
                <div class="flex justify-between items-center">
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
              </div>

              <!-- Ringkasan -->
              <div>
                <h4 class="text-sm font-semibold text-gray-600 mb-1">Ringkasan</h4>
                <p class="text-sm text-gray-700 line-clamp-3">
                  {{ stripHtml(article.summary) }}
                </p>
              </div>

              <!-- Tombol Aksi -->
              <div
                v-if="['draft', 'rejected'].includes(article.status)"
                class="flex space-x-2 pt-2 border-t border-gray-200"
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
