<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, Link } from '@inertiajs/vue3'
import { Eye, Trash2, Tag } from 'lucide-vue-next'

const props = defineProps({
  articles: Array
})

// Hapus artikel
function deleteArticle(id) {
  if (confirm('Yakin mau hapus artikel ini?')) {
    router.delete(route('admin.approved-articles.destroy', id), {
      preserveScroll: true
    })
  }
}

// Format tanggal
function formatDate(date) {
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}
</script>

<template>
  <Head title="Artikel Disetujui" />

  <AuthenticatedLayout>
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
      <h2 class="text-lg sm:text-xl font-semibold text-blue-800">Artikel Disetejui</h2>
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


    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-gray-700">
              <tr>
                <th class="p-3 text-left font-medium">Judul</th>
                <th class="p-3 text-left font-medium">Kategori</th>
                <th class="p-3 text-left font-medium">
                  <div class="flex items-center gap-1">
                    <Eye class="w-4 h-4 text-blue-500" /> Hits
                  </div>
                </th>
                <th class="p-3 text-left font-medium">Tanggal Dibuat</th>
                <th class="p-3 text-left font-medium">Aksi</th>
              </tr>
            </thead>

            <tbody>
              <!-- Loop Artikel -->
              <tr
                v-for="article in articles"
                :key="article.id"
                class="hover:bg-gray-50 transition duration-200 border-b border-gray-100"
              >
                <!-- Judul -->
                <td class="p-3 font-medium text-gray-800">
                  {{ article.title }}
                </td>

                <!-- Kategori -->
                <td class="p-3 text-left">
                  <div class="flex items-center gap-2">
                    <Tag class="w-4 h-4 text-blue-500" />
                    <span>{{ article.category || 'Tidak ada' }}</span>
                  </div>
                </td>

                <!-- Hits -->
                <td class="p-3 text-left text-gray-600">
                  <div class="flex items-center gap-1">
                    <Eye class="w-4 h-4 text-blue-500" />
                    <span>{{ article.hits }}</span>
                  </div>
                </td>

                <!-- Tanggal -->
                <td class="p-3 text-left text-gray-500">
                  {{ formatDate(article.created_at) }}
                </td>

                <!-- Tombol Hapus -->
                <td class="p-3 text-left">
                  <button
                    @click="deleteArticle(article.id)"
                    class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-xs font-medium flex items-center gap-1 transition duration-150"
                  >
                    <Trash2 class="w-4 h-4" /> Hapus
                  </button>
                </td>
              </tr>

              <!-- Jika Kosong -->
              <tr v-if="articles.length === 0">
                <td colspan="5" class="text-center p-6 text-gray-500 italic">
                  Tidak ada artikel disetujui
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
