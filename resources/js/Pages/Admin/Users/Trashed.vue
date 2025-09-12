<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Trash2, RotateCcw, ArrowLeft } from 'lucide-vue-next'
import Swal from 'sweetalert2'

const props = defineProps({
  users: Array
})

// 🔹 Restore user
const restore = (id) => {
  Swal.fire({
    title: 'Restore user ini?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, restore',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(route('admin.users.restore', id), {}, {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire('Ter-restore!', 'User berhasil dikembalikan.', 'success')
        },
        onError: () => {
          Swal.fire('Gagal!', 'User gagal di-restore.', 'error')
        }
      })
    }
  })
}

// 🔹 Hapus permanen
const forceDelete = (id) => {
  Swal.fire({
    title: 'Hapus permanen user?',
    text: "User akan hilang selamanya!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Ya, hapus permanen',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('admin.users.forceDelete', id), {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire('Terhapus!', 'User berhasil dihapus permanen.', 'success')
        },
        onError: () => {
          Swal.fire('Gagal!', 'User gagal dihapus.', 'error')
        }
      })
    }
  })
}
</script>


<template>
  <Head title="Trashed Users" />

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
        Trashed Users
      </h2>
    </div>

    <!-- Tombol kanan -->
    <div class="flex items-center gap-2">
      <!-- Link ke Approved -->
      <Link
        :href="route('admin.users')"
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

    <!-- Konten -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow mt-6 p-6">
      <div v-if="users.length" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Nama</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Email</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Username</th>
              <th class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-4 py-2">{{ user.name }}</td>
              <td class="px-4 py-2">{{ user.email }}</td>
              <td class="px-4 py-2">{{ user.username }}</td>
              <td class="px-4 py-2 flex gap-2">
                <button
                  @click="restore(user.id)"
                  class="inline-flex items-center gap-1 px-3 py-1 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg shadow transition"
                >
                  <RotateCcw class="w-4 h-4" /> Restore
                </button>
                <button
                  @click="forceDelete(user.id)"
                  class="inline-flex items-center gap-1 px-3 py-1 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg shadow transition"
                >
                  <Trash2 class="w-4 h-4" /> Hapus Permanen
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="text-center text-gray-500 dark:text-gray-400 py-6">
        Tidak ada user di trash.
      </div>
    </div>
  </AuthenticatedLayout>
</template>
