<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, Link } from '@inertiajs/vue3'
import { defineProps, computed, ref } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  articles: Array,
  requests: Array,
})

// 🔹 Approve artikel pakai SweetAlert
const approve = (id) => {
  Swal.fire({
    title: 'Approve artikel ini?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, approve',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(`/admin/articles/${id}/approve`, {}, {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire('Diapprove!', 'Artikel berhasil diapprove.', 'success')
        },
        onError: () => {
          Swal.fire('Gagal!', 'Artikel gagal diapprove.', 'error')
        }
      })
    }
  })
}

// 🔹 Reject artikel pakai modal + SweetAlert
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
    Swal.fire('Oops!', 'Alasan penolakan wajib diisi.', 'warning')
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
        Swal.fire('Ditolak!', 'Artikel berhasil ditolak.', 'success')
      },
      onError: () => {
        Swal.fire('Gagal!', 'Artikel gagal ditolak.', 'error')
      }
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

const handleRequestAction = (id, action) => {
  router.put(route('admin.article-requests.update', id), { action }, {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire('Berhasil!', `Permohonan ${action} diproses.`, 'success')
    },
    onError: () => {
      Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error')
    }
  })
}

</script>


<template>
  <AuthenticatedLayout>
    <Head title="Artikel Pending & Ditolak" />

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
      <h2 class="text-lg sm:text-xl font-semibold text-blue-800 truncate">Manajemen Artikel</h2>
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
<div v-else class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
  <div
    v-for="article in filteredArticles"
    :key="article.id"
    class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition flex flex-col"
  >
    <!-- Cover -->
    <div class="relative w-full aspect-video">
      <img
        v-if="article.cover_url"
        :src="article.cover_url"
        alt="Cover Artikel"
        class="w-full h-full object-cover rounded-t-xl"
      />
      <div
        v-else
        class="w-full h-full flex items-center justify-center text-gray-400 text-sm italic bg-gray-100 rounded-t-xl"
      >
        Tidak ada cover
      </div>
    </div>

    <!-- Konten -->
    <div class="flex flex-col flex-grow p-4 space-y-3">
      <!-- Judul + Status + Kategori -->
      <div class="flex flex-wrap justify-between items-center gap-2">
        <h3 class="text-base font-semibold text-gray-800 line-clamp-1">
          {{ article.title }}
        </h3>
        <div class="flex gap-2">
          <span
            class="px-2 py-0.5 text-[11px] font-medium rounded-full capitalize"
            :class="{
              'bg-yellow-100 text-yellow-800': article.status === 'pending',
              'bg-green-100 text-green-800': article.status === 'approved',
              'bg-red-100 text-red-800': article.status === 'rejected',
            }"
          >
            {{ article.status }}
          </span>
          <span
            v-if="article.category"
            class="px-2 py-0.5 text-[11px] font-medium rounded-full bg-blue-100 text-blue-800 capitalize"
          >
            {{ article.category }}
          </span>
        </div>
      </div>

      <!-- Ringkasan -->
      <p class="text-sm text-gray-600 line-clamp-3">
        {{ stripHtml(article.summary) }}
      </p>
    </div>

    <!-- Footer Aksi -->
    <div
      class="flex justify-between items-center gap-2 p-4 border-t border-gray-100"
    >
      <button
        @click="openModal(article)"
        class="bg-blue-500 text-white text-xs px-4 py-1.5 rounded hover:bg-blue-600 transition"
      >
        Detail
      </button>

      <div v-if="article.status === 'pending'" class="flex gap-2">
        <button
          @click="approve(article.id)"
          class="bg-green-600 text-white text-xs px-4 py-1.5 rounded hover:bg-green-700 transition"
        >
          Setujui
        </button>
        <button
          @click="openRejectModal(article.id)"
          class="bg-red-600 text-white text-xs px-4 py-1.5 rounded hover:bg-red-700 transition"
        >
          Tolak
        </button>
      </div>
    </div>
  </div>
</div>

  <!-- 🔹 Daftar Permohonan Artikel -->
  <div class="mt-10">
    <h2 class="text-lg font-semibold mb-4">Permohonan Artikel</h2>

    <div v-if="!props.requests.length" class="text-gray-500 text-sm italic">
      Tidak ada permohonan artikel.
    </div>

    <div v-else class="bg-white rounded-lg shadow p-4">
      <DataTable
        :value="props.requests"
        stripedRows
        paginator
        :rows="5"
        :rowsPerPageOptions="[5,10,20]"
        responsiveLayout="scroll"
        class="text-sm"
      >
        <!-- Artikel -->
        <Column field="article.title" header="Artikel" sortable />

        <!-- User -->
        <Column field="user.name" header="User" sortable />

        <!-- Tipe -->
        <Column field="type" header="Tipe">
          <template #body="slotProps">
            <span class="capitalize">{{ slotProps.data.type }}</span>
          </template>
        </Column>

        <!-- Alasan -->
        <Column field="reason" header="Alasan" />

        <!-- Status -->
        <Column field="status" header="Status" sortable>
          <template #body="slotProps">
            <span
              class="px-2 py-1 rounded-full text-xs font-medium"
              :class="{
                'bg-yellow-100 text-yellow-800': slotProps.data.status === 'pending',
                'bg-green-100 text-green-800': slotProps.data.status === 'approved',
                'bg-red-100 text-red-800': slotProps.data.status === 'rejected',
              }"
            >
              {{ slotProps.data.status }}
            </span>
          </template>
        </Column>

        <!-- Aksi -->
        <Column header="Aksi" :exportable="false">
          <template #body="slotProps">
            <div v-if="slotProps.data.status === 'pending'" class="flex gap-2">
              <Button
                label="Approve"
                size="small"
                severity="success"
                @click="handleRequestAction(slotProps.data.id, 'approve')"
              />
              <Button
                label="Reject"
                size="small"
                severity="danger"
                @click="handleRequestAction(slotProps.data.id, 'reject')"
              />
            </div>
            <span v-else class="text-gray-400 text-xs italic">Selesai</span>
          </template>
        </Column>
      </DataTable>
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
  <div
    class="bg-white w-full max-w-4xl rounded shadow-lg p-6 relative overflow-y-auto max-h-screen"
  >
    <button
      @click="closeModal"
      class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl"
    >
      &times;
    </button>

    <fieldset class="space-y-4 mb-4 border border-gray-200 rounded p-4">
      <legend class="text-sm font-semibold text-gray-700">
        Detail Artikel
      </legend>

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
        <p v-else class="text-sm text-gray-400 italic mt-1">
          Tidak ada cover
        </p>
      </div>

      <fieldset class="border border-gray-200 rounded p-2">
        <legend class="text-xs font-semibold text-gray-500 px-1">
          Ringkasan
        </legend>
        <div
          class="prose prose-gray dark:prose-invert max-w-none text-sm
            [&_h1]:text-2xl [&_h2]:text-xl [&_h3]:text-lg
            [&_ul]:list-disc [&_ul]:pl-6
            [&_ol]:list-decimal [&_ol]:pl-6
            [&_blockquote]:border-l-4 [&_blockquote]:border-gray-300 [&_blockquote]:pl-4 [&_blockquote]:italic"
          v-html="selectedArticle?.summary || 'Tidak ada ringkasan'"
        ></div>
      </fieldset>

      <div>
        <label class="block text-xs text-gray-600">Isi Artikel:</label>
        <div
          class="prose prose-gray dark:prose-invert max-w-none
            [&_h1]:text-3xl [&_h2]:text-2xl [&_h3]:text-xl
            [&_pre]:bg-gray-900 [&_pre]:text-white [&_pre]:p-3 [&_pre]:rounded [&_pre]:overflow-x-auto [&_pre]:font-mono [&_pre]:text-sm [&_pre]:my-2
            [&_code]:text-red-500 [&_p]:leading-relaxed
            [&_p.ql-align-center]:text-center
            [&_p.ql-align-right]:text-right
            [&_blockquote]:border-l-4 [&_blockquote]:border-gray-300 dark:[&_blockquote]:border-gray-600 [&_blockquote]:pl-4 [&_blockquote]:italic"
          v-html="selectedArticle?.content || 'Tidak tersedia'"
        ></div>
      </div>

      <div
        v-if="
          selectedArticle?.status === 'rejected' &&
          selectedArticle?.rejection_reason
        "
      >
        <label class="block text-xs text-red-600">Alasan Penolakan:</label>
        <p class="text-red-700">
          {{ selectedArticle.rejection_reason }}
        </p>
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
