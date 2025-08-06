<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'

const props = defineProps({
  articles: Array,
  filters: Object,
})

const form = useForm({})
const selectedStatuses = ref(props.filters?.status ?? [])
const search = ref(props.filters?.search ?? '')
const showModal = ref(false)

const toggleStatus = (status) => {
  if (selectedStatuses.value.includes(status)) {
    selectedStatuses.value = selectedStatuses.value.filter(s => s !== status)
  } else {
    selectedStatuses.value.push(status)
  }

  applyFilters()
}

const applyFilters = () => {
  router.get('/operator/articles/mine', {
    status: selectedStatuses.value,
    search: search.value,
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

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

const totalByStatus = computed(() => {
  const totals = {
    draft: 0,
    pending: 0,
    approved: 0,
    rejected: 0,
  }

  props.articles.forEach(article => {
    if (totals[article.status] !== undefined) {
      totals[article.status]++
    }
  })

  return totals
})
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Artikel Saya" />

    <template #header>
      <h2 class="text-2xl font-bold text-gray-800 leading-tight">Artikel Saya</h2>
    </template>

    <div class="py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 shadow-xl rounded-lg">

          <!-- FILTER DAN TOTAL BUTTON -->
          <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex-1">
              <!-- FILTER STATUS -->
              <label class="block text-sm font-medium text-gray-700 mb-2">Filter Status Artikel</label>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="status in ['draft', 'pending', 'approved', 'rejected']"
                  :key="status"
                  @click.prevent="toggleStatus(status)"
                  :class="[
                    'px-4 py-1.5 rounded-full text-sm font-medium border transition shadow-sm focus:outline-none',
                    selectedStatuses.includes(status)
                      ? 'bg-blue-600 text-white border-blue-600'
                      : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'
                  ]"
                >
                  {{ status.charAt(0).toUpperCase() + status.slice(1) }}
                </button>
              </div>
            </div>

            <!-- SEARCH BAR -->
            <div class="w-full md:w-64">
              <input
                v-model="search"
                @input="applyFilters"
                type="text"
                placeholder="Cari berdasarkan judul"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
              />
            </div>

            <!-- TOTAL ARTIKEL -->
            <div>
              <button
                @click="showModal = true"
                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition text-sm shadow"
              >
                Lihat Total Artikel
              </button>
            </div>
          </div>

          <!-- TIDAK ADA ARTIKEL -->
          <div v-if="articles.length === 0" class="text-gray-500 text-center py-10">
            Belum ada artikel yang ditemukan.
          </div>

          <!-- DAFTAR ARTIKEL -->
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="article in articles"
              :key="article.id"
              class="border border-gray-200 p-5 rounded-xl shadow-md bg-white flex flex-col space-y-3 hover:shadow-lg transition"
            >
              <div>
                <img
                  v-if="article.cover_url"
                  :src="article.cover_url"
                  alt="Cover"
                  class="w-full h-40 object-cover rounded-md"
                />
                <div v-else class="w-full h-40 bg-gray-100 flex items-center justify-center text-gray-400 text-sm italic rounded-md">
                  Tidak ada cover
                </div>
              </div>

              <div>
                <div class="flex justify-between items-center mb-1">
                  <h3 class="text-lg font-semibold text-gray-800">{{ article.title }}</h3>
                  <span
                    class="px-2 py-0.5 text-xs font-semibold rounded-full capitalize"
                    :class="{
                      'bg-yellow-100 text-yellow-800': article.status === 'pending',
                      'bg-green-100 text-green-800': article.status === 'approved',
                      'bg-red-100 text-red-800': article.status === 'rejected',
                      'bg-gray-200 text-gray-700': article.status === 'draft',
                    }"
                  >
                    {{ article.status }}
                  </span>
                </div>
              </div>

              <div class="text-sm text-gray-700 line-clamp-3">
                {{ stripHtml(article.summary) }}
              </div>

              <div
                v-if="article.status === 'rejected' && article.rejection_reason"
                class="bg-red-50 text-red-700 text-xs p-3 rounded-md border border-red-200"
              >
                <strong>Alasan Ditolak:</strong> {{ article.rejection_reason }}
              </div>

              <div
                v-if="['draft', 'rejected', 'pending'].includes(article.status)"
                class="flex justify-end space-x-2 pt-2 border-t border-gray-100 mt-auto"
              >
                <Link
                  :href="`/operator/articles/${article.id}/edit`"
                  class="bg-blue-600 text-white text-xs px-4 py-1.5 rounded hover:bg-blue-700 transition"
                >
                  Edit
                </Link>
                <button
                  @click="destroy(article.id)"
                  class="bg-red-600 text-white text-xs px-4 py-1.5 rounded hover:bg-red-700 transition"
                >
                  Hapus
                </button>
              </div>
            </div>
          </div>

         <!-- MODAL TOTAL ARTIKEL -->
<transition name="fade">
  <div
    v-if="showModal"
    class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center"
  >
    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md relative">
      <!-- Title -->
      <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Statistik Artikel
      </h3>

      <!-- Status Counts -->
      <ul class="space-y-3 text-sm text-gray-700">
        <li class="flex items-center justify-between">
          <span class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-gray-400"></span>
            Draft
          </span>
          <strong>{{ totalByStatus.draft }}</strong>
        </li>
        <li class="flex items-center justify-between">
          <span class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
            Pending
          </span>
          <strong>{{ totalByStatus.pending }}</strong>
        </li>
        <li class="flex items-center justify-between">
          <span class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-green-500"></span>
            Approved
          </span>
          <strong>{{ totalByStatus.approved }}</strong>
        </li>
        <li class="flex items-center justify-between">
          <span class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-red-500"></span>
            Rejected
          </span>
          <strong>{{ totalByStatus.rejected }}</strong>
        </li>
      </ul>

      <!-- Close Button -->
      <button
        @click="showModal = false"
        class="mt-6 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-md transition duration-150"
      >
        Tutup
      </button>
    </div>
  </div>
</transition>


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
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
