<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Swal from 'sweetalert2'

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

// 🔹 SweetAlert delete
const destroy = (id) => {
  Swal.fire({
    title: 'Yakin ingin menghapus artikel ini?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      form.delete(`/operator/articles/${id}`, {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Artikel berhasil dihapus!',
            timer: 1500,
            showConfirmButton: false
          })
        },
        onError: () => {
          Swal.fire({
            icon: 'error',
            title: 'Gagal menghapus artikel',
            text: 'Coba lagi nanti.'
          })
        }
      })
    }
  })
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

// 🔹 Request action
const requestAction = (id) => {
  Swal.fire({
    title: 'Kirim permohonan?',
    input: 'textarea',
    inputLabel: 'Alasan Permohonan',
    inputPlaceholder: 'Tuliskan alasan permohonan di sini...',
    inputAttributes: {
      'aria-label': 'Tuliskan alasan'
    },
    showCancelButton: true,
    confirmButtonText: 'Kirim',
    cancelButtonText: 'Batal',
    inputValidator: (value) => {
      if (!value) return 'Alasan wajib diisi!'
    }
  }).then((result) => {
    if (result.isConfirmed) {
     router.post(`/operator/articles/${id}/request`, {
  type: 'to_draft',
  reason: result.value
}, {
  preserveScroll: true,
  onSuccess: () => {
    Swal.fire({
      icon: 'success',
      title: 'Permohonan berhasil dikirim!',
      timer: 1500,
      showConfirmButton: false
    })
  },
   onError: (errors) => {
    Swal.fire({
      icon: 'error',
      title: errors?.response?.statusText || 'Gagal kirim permohonan',
      text: errors?.response?.data?.message || 'Gagal Mengirim Permohonan Hal ini dapat terjadi jika kamu sudah mengirim permohonan untuk artikel ini.'
    })
  }
})

    }
  })
}






</script>


<template>
  <AuthenticatedLayout>
    <Head title="Artikel Saya" />
<template #header>
  <!-- Wrapper full width -->
  <div class="w-full flex justify-between items-center bg-blue-100 border border-blue-200 rounded-lg px-4 py-3 shadow-sm">

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
      <h2 class="text-lg sm:text-xl font-semibold text-blue-800 truncate">Artikel Saya</h2>
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



    <div class="py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 shadow-xl rounded-lg">

          <!-- Filter -->
          <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex-1">
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

            <div class="w-full md:w-64">
              <input
                v-model="search"
                @input="applyFilters"
                type="text"
                placeholder="Cari berdasarkan judul"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
              />
            </div>

            <div>
              <button
                @click="showModal = true"
                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition text-sm shadow"
              >
                Lihat Total Artikel
              </button>
            </div>
          </div>

          <!-- Tidak ada artikel -->
          <div v-if="articles.length === 0" class="text-gray-500 text-center py-10">
            Belum ada artikel yang ditemukan.
          </div>

<!-- Daftar artikel -->
<div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
  <div
    v-for="article in articles"
    :key="article.id"
    class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition flex flex-col"
  >
    <!-- Cover -->
    <div class="relative w-full aspect-video">
      <img
        v-if="article.cover_url"
        :src="article.cover_url"
        alt="Cover"
        class="w-full h-full object-cover rounded-t-xl"
      />
      <div
        v-else
        class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400 text-xs italic rounded-t-xl"
      >
        Tidak ada cover
      </div>
      <span
        v-if="article.category"
        class="absolute top-2 right-2 bg-indigo-600 text-white text-[10px] px-2 py-0.5 rounded-full uppercase shadow-sm"
      >
        {{ article.category }}
      </span>
    </div>

    <!-- Isi -->
    <div class="flex flex-col flex-grow p-4 divide-y divide-gray-100">
      <!-- Judul + Status -->
      <div class="flex items-center justify-between pb-2">
        <h3 class="text-base font-semibold text-gray-800 line-clamp-1">
          {{ article.title }}
        </h3>
        <span
          class="px-2 py-0.5 text-[11px] font-medium rounded-full capitalize"
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

      <!-- Ringkasan -->
      <div class="pt-2 text-sm text-gray-600 line-clamp-3">
        {{ stripHtml(article.summary) }}
      </div>

      <!-- Alasan / Approved note -->
      <div v-if="article.status === 'rejected' && article.rejection_reason" class="pt-2 text-xs text-red-700 bg-red-50 border border-red-200 rounded-md p-2">
        <strong>Alasan Ditolak:</strong> {{ article.rejection_reason }}
      </div>
      <div v-if="article.status === 'approved'" class="pt-2 text-xs text-green-700 bg-green-50 border border-green-200 rounded-md p-2">
        <strong>Approved</strong>
      </div>

     <!-- Aksi -->
<div class="flex justify-end gap-2 pt-3">
  <!-- Kalau draft, rejected, pending: boleh edit & hapus -->
  <template v-if="['draft', 'rejected', 'pending'].includes(article.status)">
    <Link
      :href="`/operator/articles/${article.id}/edit`"
      class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded hover:bg-blue-700 transition"
    >
      Edit
    </Link>
    <button
      @click="destroy(article.id)"
      class="bg-red-600 text-white text-xs px-3 py-1.5 rounded hover:bg-red-700 transition"
    >
      Hapus
    </button>
  </template>

  <!-- Kalau sudah approved: tampilkan tombol Request -->
  <template v-else-if="article.status === 'approved'">
    <button
      @click="requestAction(article.id)"
      class="bg-indigo-600 text-white text-xs px-3 py-1.5 rounded hover:bg-indigo-700 transition"
    >
      Request
    </button>
  </template>
</div>

    </div>
  </div>
</div>





          <!-- Modal Statistik -->
          <transition name="fade">
            <div
              v-if="showModal"
              class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center"
            >
              <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md relative">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                  <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Statistik Artikel
                </h3>

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
