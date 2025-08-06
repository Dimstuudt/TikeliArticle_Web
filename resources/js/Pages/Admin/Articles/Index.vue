<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { defineProps, computed, ref } from 'vue'

const props = defineProps({
  articles: Array,
})

const approve = (id) => {
  router.put(`/admin/articles/${id}/approve`, {}, { preserveScroll: true })
}

const reject = (id) => {
  router.put(`/admin/articles/${id}/reject`, {}, { preserveScroll: true })
}

const stripHtml = (html) => {
  const div = document.createElement('div')
  div.innerHTML = html
  return div.textContent || div.innerText || ''
}

const filteredArticles = computed(() =>
  props.articles.filter(article =>
    ['pending', 'rejected'].includes(article.status)
  )
)

const showModal = ref(false)
const selectedArticle = ref(null)

const openModal = (article) => {
  selectedArticle.value = article
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedArticle.value = null
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Artikel Pending & Ditolak" />

    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Artikel</h2>
    </template>

    <div class="py-8">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">

          <div v-if="filteredArticles.length === 0" class="text-gray-500 text-center">
            Tidak ada artikel yang perlu ditinjau.
          </div>

          <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="article in filteredArticles"
              :key="article.id"
              class="border border-gray-200 rounded-lg shadow-sm p-4 bg-white space-y-4"
            >
              <div>
                <img
                  v-if="article.cover_url"
                  :src="article.cover_url"
                  alt="Cover Artikel"
                  class="w-full h-40 object-cover rounded"
                />
                <div v-else class="text-sm text-gray-400 italic">Tidak ada cover</div>
              </div>

              <div>
                <span class="text-sm font-semibold text-gray-600">Judul:</span>
                <p class="text-base font-medium text-gray-800 mt-1">{{ article.title }}</p>
              </div>

              <div>
                <span class="text-sm font-semibold text-gray-600">Ringkasan:</span>
                <p class="text-sm text-gray-700 mt-1 line-clamp-3">
                  {{ stripHtml(article.summary) }}
                </p>
              </div>

              <div>
                <span class="text-sm font-semibold text-gray-600">Status:</span>
                <span
                  class="inline-block mt-1 px-2 py-1 text-xs font-medium rounded-full"
                  :class="{
                    'bg-yellow-100 text-yellow-800': article.status === 'pending',
                    'bg-red-100 text-red-800': article.status === 'rejected',
                  }"
                >
                  {{ article.status }}
                </span>
              </div>

              <div class="flex flex-wrap justify-between items-center gap-2 pt-2 border-t border-gray-200">
                <button
                  @click="openModal(article)"
                  class="text-blue-600 text-xs hover:underline"
                >
                  Lihat Detail
                </button>

                <div v-if="article.status === 'pending'" class="flex gap-2">
                  <button
                    @click="approve(article.id)"
                    class="bg-green-600 text-white text-xs px-4 py-1 rounded hover:bg-green-700"
                  >
                    Setujui
                  </button>
                  <button
                    @click="reject(article.id)"
                    class="bg-red-600 text-white text-xs px-4 py-1 rounded hover:bg-red-700"
                  >
                    Tolak
                  </button>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Modal Detail -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center"
    >
      <div class="bg-white w-full max-w-2xl rounded shadow-lg p-6 relative overflow-y-auto max-h-screen">
        <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>

        <h3 class="text-lg font-semibold mb-2">Detail Artikel</h3>

        <div class="mb-4">
          <span class="font-semibold text-sm">Judul:</span>
          <p class="text-gray-800">{{ selectedArticle?.title }}</p>
        </div>

        <div class="mb-4">
          <span class="font-semibold text-sm">Cover:</span>
          <img
            v-if="selectedArticle?.cover_url"
            :src="selectedArticle.cover_url"
            alt="Cover"
            class="w-full h-48 object-cover mt-1 rounded"
          />
          <p v-else class="text-sm text-gray-400 italic mt-1">Tidak ada cover</p>
        </div>

        <div class="mb-4">
          <span class="font-semibold text-sm">Ringkasan:</span>
          <p class="text-gray-700">{{ stripHtml(selectedArticle?.summary) }}</p>
        </div>

        <div class="mb-4">
          <span class="font-semibold text-sm">Isi Artikel:</span>
          <div class="prose max-w-full" v-html="selectedArticle?.content || 'Tidak tersedia'"></div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
