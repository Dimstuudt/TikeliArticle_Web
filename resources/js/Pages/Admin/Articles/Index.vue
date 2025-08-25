<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { defineProps, computed, ref } from 'vue'

const props = defineProps({
  articles: Array,
})

// Approve artikel
const approve = (id) => {
  router.put(`/admin/articles/${id}/approve`, {}, { preserveScroll: true })
}

// Reject modal
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

// Strip HTML dari ringkasan
const stripHtml = (html) => {
  const div = document.createElement('div')
  div.innerHTML = html
  return div.textContent || div.innerText || ''
}

// Filter artikel pending & rejected
const filteredArticles = computed(() =>
  props.articles.filter(article =>
    ['pending', 'rejected'].includes(article.status)
  )
)

// Modal detail artikel
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
  <div class="flex items-center justify-between bg-blue-100 border border-blue-200 rounded-lg px-4 py-3 shadow-sm">
    <!-- Judul -->
    <div class="flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg"
           class="h-6 w-6 text-blue-600"
           fill="none"
           viewBox="0 0 24 24"
           stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M5.121 17.804A13.937 13.937 0 0112 15c2.21 0 4.29.534 6.121 1.475M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
      <h2 class="text-lg sm:text-xl font-semibold text-blue-800"> Manajemen Artikel</h2>
    </div>

    <!-- Tombol ke Artikel -->
    <Link
      :href="route('guest.welcome')"
      class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium rounded-lg shadow transition"
    >
      📖 Lihat Artikel
    </Link>
  </div>
</template>


    <div class="py-8">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">

          <!-- Pesan jika tidak ada artikel -->
          <div v-if="filteredArticles.length === 0" class="text-gray-500 text-center">
            Tidak ada artikel yang perlu ditinjau.
          </div>

          <!-- Grid Artikel -->
          <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="article in filteredArticles"
              :key="article.id"
              class="border border-gray-200 rounded-lg shadow p-4 bg-white flex flex-col justify-between"
            >
              <!-- Informasi Artikel -->
              <fieldset class="space-y-2">
                <legend class="text-sm font-bold text-gray-700 mb-2">Informasi Artikel</legend>

                <!-- Cover Artikel -->
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

                <!-- Judul Artikel -->
                <div>
                  <label class="block text-xs text-gray-600">Judul:</label>
                  <p class="text-base font-medium text-gray-800">{{ article.title }}</p>
                </div>
              </fieldset>

              <!-- Ringkasan Artikel dengan scroll jika panjang -->
              <fieldset class="border border-gray-200 rounded p-2 mt-2 flex-1 overflow-hidden">
                <legend class="text-xs font-semibold text-gray-500 px-1">Ringkasan</legend>
                <div
                  class="text-sm text-gray-700 overflow-y-auto"
                  style="min-height: 3rem; max-height: 6rem;"
                >
                  {{ stripHtml(article.summary) }}
                </div>
              </fieldset>

              <!-- Status & Kategori -->
              <div class="mt-2">
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

              <!-- Tombol Aksi: Lihat, Setujui, Tolak -->
              <div class="flex flex-wrap justify-between items-center gap-2 pt-2 border-t border-gray-200 mt-2">
                <button
                  @click="openModal(article)"
                  class="bg-blue-500 text-white text-xs px-4 py-1 rounded hover:bg-blue-600 transition"
                >
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
/* Membatasi ringkasan agar scroll internal muncul */
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
