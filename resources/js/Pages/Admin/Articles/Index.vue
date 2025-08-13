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

const showRejectModal = ref(false)
const rejectReason = ref('')
const rejectingArticleId = ref(null)

const openRejectModal = (id) => {
  rejectingArticleId.value = id
  rejectReason.value = ''
  showRejectModal.value = true
}

const reject = () => {
  if (!rejectReason.value.trim()) {
    alert('Alasan penolakan wajib diisi.')
    return
  }

  router.put(
    `/admin/articles/${rejectingArticleId.value}/reject`,
    { reason: rejectReason.value },
    {
      preserveScroll: true,
      onSuccess: () => {
        showRejectModal.value = false
        rejectingArticleId.value = null
      },
    }
  )
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
              class="border border-gray-200 rounded-lg shadow p-4 bg-white space-y-4"
            >
              <fieldset class="space-y-2">
                <legend class="text-sm font-bold text-gray-700 mb-2">Informasi Artikel</legend>

                <div>
                  <label class="block text-xs text-gray-600">Cover:</label>
                  <img
                    v-if="article.cover_url"
                    :src="article.cover_url"
                    alt="Cover Artikel"
                    class="w-full h-40 object-cover rounded"
                  />
                  <div v-else class="text-sm text-gray-400 italic">Tidak ada cover</div>
                </div>

                <div>
                  <label class="block text-xs text-gray-600">Judul:</label>
                  <p class="text-base font-medium text-gray-800">{{ article.title }}</p>
                </div>
              </fieldset>

              <fieldset class="border border-gray-200 rounded p-2">
                <legend class="text-xs font-semibold text-gray-500 px-1">Ringkasan</legend>
                <p class="text-sm text-gray-700 line-clamp-3">{{ stripHtml(article.summary) }}</p>
              </fieldset>

              <div>
                <label class="block text-xs text-gray-600">Status & Kategori:</label>
                <div class="flex flex-wrap gap-2 mt-1">
                  <span
                    class="inline-block px-2 py-1 text-xs font-semibold rounded-full"
                    :class="{
                      'bg-yellow-100 text-yellow-800': article.status === 'pending',
                      'bg-red-100 text-red-800': article.status === 'rejected',
                    }"
                  >
                    {{ article.status }}
                  </span>
                  <span
                    class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 capitalize"
                  >
                    {{ article.category }}
                  </span>
                </div>
              </div>

              <div class="flex flex-wrap justify-between items-center gap-2 pt-2 border-t border-gray-200">
                <button @click="openModal(article)" class="bg-blue-500 text-white text-xs px-4 py-1 rounded hover:bg-blue-600 transition">
                  Lihat Detail
                </button>
                <div v-if="article.status === 'pending'" class="flex gap-2">
                  <button
                    @click="approve(article.id)"
                    class="bg-green-600 text-white text-xs px-4 py-1 rounded hover:bg-green-700 transition"
                  >
                    Setujui
                  </button>
                  <button
                    @click="openRejectModal(article.id)"
                    class="bg-red-600 text-white text-xs px-4 py-1 rounded hover:bg-red-700 transition"
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

    <!-- Modal Detail Artikel -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center"
    >
      <div class="bg-white w-full max-w-2xl rounded shadow-lg p-6 relative overflow-y-auto max-h-screen">
        <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>

        <fieldset class="space-y-4 mb-4 border border-gray-200 rounded p-4">
          <legend class="text-sm font-semibold text-gray-700">Detail Artikel</legend>

          <div>
            <label class="block text-xs text-gray-600">Judul:</label>
            <p class="text-gray-800">{{ selectedArticle?.title }}</p>
          </div>

          <div>
            <label class="block text-xs text-gray-600">Cover:</label>
            <img
              v-if="selectedArticle?.cover_url"
              :src="selectedArticle.cover_url"
              alt="Cover"
              class="w-full h-48 object-cover mt-1 rounded"
            />
            <p v-else class="text-sm text-gray-400 italic mt-1">Tidak ada cover</p>
          </div>

          <fieldset class="border border-gray-200 rounded p-2">
            <legend class="text-xs font-semibold text-gray-500 px-1">Ringkasan</legend>
            <p class="text-sm text-gray-700">{{ stripHtml(selectedArticle?.summary) }}</p>
          </fieldset>

          <div>
            <label class="block text-xs text-gray-600">Isi Artikel:</label>
            <div class="prose max-w-full" v-html="selectedArticle?.content || 'Tidak tersedia'"></div>
          </div>

          <div v-if="selectedArticle?.status === 'rejected' && selectedArticle?.rejection_reason">
            <label class="block text-xs text-red-600">Alasan Penolakan:</label>
            <p class="text-red-700">{{ selectedArticle.rejection_reason }}</p>
          </div>
        </fieldset>
      </div>
    </div>

    <!-- Modal Alasan Penolakan -->
    <div
      v-if="showRejectModal"
      class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center"
    >
      <div class="bg-white w-full max-w-md rounded shadow-lg p-6 relative">
        <button @click="showRejectModal = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>

        <h3 class="text-lg font-semibold mb-4">Alasan Penolakan</h3>

        <textarea
          v-model="rejectReason"
          rows="4"
          placeholder="Masukkan alasan penolakan..."
          class="w-full border border-gray-300 rounded p-2 text-sm"
        ></textarea>

        <div class="mt-4 flex justify-end gap-2">
          <button @click="showRejectModal = false" class="px-4 py-1 text-sm text-gray-700 hover:underline">Batal</button>
          <button @click="reject" class="px-4 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">Kirim</button>
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
