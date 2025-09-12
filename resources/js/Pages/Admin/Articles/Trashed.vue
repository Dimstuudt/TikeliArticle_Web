<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  articles: Array,
})

// Restore artikel
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
        onSuccess: () => {
          Swal.fire('Ter-restore!', 'Artikel berhasil dikembalikan.', 'success')
        },
        onError: () => {
          Swal.fire('Gagal!', 'Artikel gagal di-restore.', 'error')
        }
      })
    }
  })
}

// Hapus permanen artikel
const forceDelete = (id) => {
  Swal.fire({
    title: 'Hapus permanen artikel?',
    text: "Artikel akan hilang selamanya!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Ya, hapus permanen',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/admin/articles/approved/${id}/force`, {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire('Terhapus!', 'Artikel dihapus permanen.', 'success')
        },
        onError: () => {
          Swal.fire('Gagal!', 'Artikel gagal dihapus.', 'error')
        }
      })
    }
  })
}
</script>

<template>
  <Head title="Artikel Dihapus" />

  <AuthenticatedLayout>
<template #header>
  <!-- Wrapper full width -->
  <div
    class="w-full flex justify-between items-center bg-yellow-100 border border-yellow-200 rounded-lg px-4 py-3 shadow-sm"
  >
    <!-- Judul -->
    <div class="flex items-center gap-2">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 text-yellow-600"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M20.25 7.5l-8.25 8.25L7.5 11.25"
        />
      </svg>
      <h2 class="text-lg sm:text-xl font-semibold text-yellow-800 truncate">
        Trashed Articles
      </h2>
    </div>

    <!-- Tombol kanan -->
    <div class="flex items-center gap-2">
      <!-- Link ke Approved -->
      <Link
        :href="route('admin.approved-articles.index')"
        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium rounded-lg shadow transition"
      >
        ✅ Approved
      </Link>

      <!-- Link ke Artikel Publik -->
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
      <div v-if="articles.length > 0">
        <table class="w-full border border-gray-300 dark:border-gray-700">
          <thead class="bg-gray-100 dark:bg-gray-700">
            <tr>
              <th class="p-2 text-left">Judul</th>
              <th class="p-2">Kategori</th>
              <th class="p-2">Tanggal Hapus</th>
              <th class="p-2">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="article in articles" :key="article.id" class="border-t dark:border-gray-700">
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
      </div>

      <div v-else class="text-gray-600 dark:text-gray-300">
        Tidak ada artikel yang dihapus.
      </div>
    </div>
  </AuthenticatedLayout>
</template>
