<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  articles: Array,
})

const search = ref('')
const selectedArticles = ref([])

// 🔎 Filtering pencarian
const filteredArticles = computed(() => {
  if (!search.value) return props.articles
  return props.articles.filter(article =>
    article.title.toLowerCase().includes(search.value.toLowerCase()) ||
    article.category?.toLowerCase().includes(search.value.toLowerCase())
  )
})

// 🔹 Restore tunggal
const restore = (id) => {
  Swal.fire({
    title: 'Restore artikel ini?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, restore',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(`/admin/articles/approved/${id}/restore`, {}, {
        preserveScroll: true,
        onSuccess: () => Swal.fire('Sukses!', 'Artikel berhasil direstore.', 'success'),
        onError: () => Swal.fire('Gagal!', 'Artikel gagal direstore.', 'error'),
      })
    }
  })
}

// 🔹 Hapus permanen tunggal
const forceDelete = (id) => {
  Swal.fire({
    title: 'Hapus permanen artikel?',
    text: "Artikel akan hilang selamanya!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus permanen',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#d33'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/admin/articles/approved/${id}/force`, {
        preserveScroll: true,
        onSuccess: () => Swal.fire('Terhapus!', 'Artikel dihapus permanen.', 'success'),
        onError: () => Swal.fire('Gagal!', 'Artikel gagal dihapus.', 'error'),
      })
    }
  })
}

// 🔹 Bulk Restore
const bulkRestore = () => {
  if (selectedArticles.value.length === 0) {
    Swal.fire('Oops!', 'Pilih minimal satu artikel dulu.', 'warning')
    return
  }
  Swal.fire({
    title: `Restore ${selectedArticles.value.length} artikel?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, restore',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(`/admin/articles/approved/bulk-restore`, {
        ids: selectedArticles.value.map(a => a.id)
      }, {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire('Sukses!', 'Artikel berhasil direstore.', 'success')
          selectedArticles.value = []
        }
      })
    }
  })
}

// 🔹 Bulk Force Delete
const bulkForceDelete = () => {
  if (selectedArticles.value.length === 0) {
    Swal.fire('Oops!', 'Pilih minimal satu artikel dulu.', 'warning')
    return
  }
  Swal.fire({
    title: `Hapus permanen ${selectedArticles.value.length} artikel?`,
    text: "Artikel akan hilang selamanya!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus permanen',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#d33'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/admin/articles/approved/bulk-force`, {
        data: { ids: selectedArticles.value.map(a => a.id) }
      }, {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire('Terhapus!', 'Artikel berhasil dihapus permanen.', 'success')
          selectedArticles.value = []
        }
      })
    }
  })
}
</script>

<template>
  <Head title="Trashed Articles" />

  <AuthenticatedLayout>
    <template #header>
      <div class="w-full flex justify-between items-center bg-yellow-100 border border-yellow-200 rounded-lg px-4 py-3 shadow-sm">
        <div class="flex items-center gap-2">
          📂
          <h2 class="text-lg sm:text-xl font-semibold text-yellow-800 truncate">
            Trashed Articles
          </h2>
        </div>

        <div class="flex items-center gap-2">
          <Link
            :href="route('admin.approved-articles.index')"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium rounded-lg shadow transition"
          >
            ✅ Approved
          </Link>
          <Link
            :href="route('guest.welcome')"
            class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 text-sm font-medium rounded-lg shadow transition"
          >
            📖 Lihat Artikel
          </Link>
        </div>
      </div>
    </template>

    <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
      <!-- Tombol Bulk -->
      <div class="flex gap-2 mb-4">
        <button
          @click="bulkRestore"
          class="px-4 py-2 bg-green-600 text-white rounded shadow hover:bg-green-700 disabled:opacity-50"
          :disabled="selectedArticles.length === 0"
        >
          Restore Terpilih ({{ selectedArticles.length }})
        </button>
        <button
          @click="bulkForceDelete"
          class="px-4 py-2 bg-red-600 text-white rounded shadow hover:bg-red-700 disabled:opacity-50"
          :disabled="selectedArticles.length === 0"
        >
          Hapus Permanen ({{ selectedArticles.length }})
        </button>
      </div>

      <!-- Search -->
      <div class="mb-4">
        <input
          v-model="search"
          type="text"
          placeholder="Cari artikel..."
          class="border rounded px-3 py-1 w-full sm:w-1/3"
        />
      </div>

      <!-- Tabel -->
      <table class="w-full border border-gray-300 dark:border-gray-700">
        <thead class="bg-gray-100 dark:bg-gray-700">
          <tr>
            <th class="p-2"><input type="checkbox" @change="(e) => {
              if (e.target.checked) {
                selectedArticles = [...filteredArticles]
              } else {
                selectedArticles = []
              }
            }" /></th>
            <th class="p-2 text-left">Judul</th>
            <th class="p-2">Kategori</th>
            <th class="p-2">Tanggal Hapus</th>
            <th class="p-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="article in filteredArticles" :key="article.id" class="border-t dark:border-gray-700">
            <td class="p-2 text-center">
              <input type="checkbox" :value="article" v-model="selectedArticles" />
            </td>
            <td class="p-2">{{ article.title }}</td>
            <td class="p-2 text-center">{{ article.category }}</td>
            <td class="p-2 text-center">{{ article.deleted_at }}</td>
            <td class="p-2 flex gap-2 justify-center">
              <button
                @click="restore(article.id)"
                class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700"
              >
                Restore
              </button>
              <button
                @click="forceDelete(article.id)"
                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
              >
                Hapus Permanen
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="!filteredArticles.length" class="text-center text-gray-500 py-6">
        Tidak ada artikel di trash.
      </div>
    </div>
  </AuthenticatedLayout>
</template>
