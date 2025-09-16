<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, Link } from '@inertiajs/vue3'
import { Eye, Tag, Archive } from 'lucide-vue-next'
import Swal from 'sweetalert2'

// PrimeVue Components
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'

import { ref, computed } from 'vue'

const props = defineProps({
  articles: Array
})

const search = ref('')
const selectedArticles = ref([])

// Filtered articles based on search
const filteredArticles = computed(() => {
  if (!search.value) return props.articles
  return props.articles.filter(article =>
    article.title.toLowerCase().includes(search.value.toLowerCase())
  )
})

// Format tanggal
function formatDate(date) {
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

// Hapus artikel tunggal
function deleteArticle(id) {
  Swal.fire({
    title: 'Yakin mau hapus artikel ini?',
    text: "Artikel akan masuk ke trash (soft delete)",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(route('admin.approved-articles.destroy', id), {
        preserveScroll: true,
        onSuccess: () => Swal.fire('Terhapus!', 'Artikel berhasil dihapus.', 'success'),
        onError: () => Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus artikel.', 'error')
      });
    }
  })
}

// Bulk delete
function deleteSelectedArticles() {
  if (selectedArticles.value.length === 0) {
    Swal.fire('Oops!', 'Tidak ada artikel yang dipilih', 'warning')
    return
  }

  Swal.fire({
    title: `Hapus ${selectedArticles.value.length} artikel?`,
    text: "Artikel akan masuk ke trash (soft delete)",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  }).then(result => {
    if (result.isConfirmed) {
      const ids = selectedArticles.value.map(a => a.id)

      // Ganti delete ke post dengan spoof method
      router.post(route('admin.approved-articles.bulkDestroy'), {
        _method: 'delete', // method spoofing
        ids
      }, {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire('Terhapus!', 'Artikel berhasil dihapus.', 'success')
          selectedArticles.value = []
        },
        onError: () => Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus artikel.', 'error')
      })
    }
  })
}
</script>


<template>
  <Head title="Artikel Disetujui" />

  <AuthenticatedLayout>
    <template #header>
      <div class="w-full flex justify-between items-center bg-blue-100 border border-blue-200 rounded-lg px-4 py-3 shadow-sm">
        <div class="flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.21 0 4.29.534 6.121 1.475M15 10a3 3 0 11-6 0 3 0 016 0z"/>
          </svg>
          <h2 class="text-lg sm:text-xl font-semibold text-blue-800 truncate">Approved Articles</h2>
        </div>

        <div class="flex items-center gap-2">
          <Link :href="route('admin.approved-articles.trashed')" class="inline-flex items-center gap-2 bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 text-sm font-medium rounded-lg shadow transition">
            <Archive class="w-4 h-4" /> Trash
          </Link>

          <Link :href="route('guest.welcome')" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium rounded-lg shadow transition">
            📖 Lihat Artikel
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl sm:rounded-lg p-6">

          <!-- Search + Bulk Delete -->
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <span class="p-input-icon-lef w-full sm:w-1/3">

              <InputText v-model="search" placeholder="Cari judul..." class="w-full"/>
            </span>

            <Button
              label="Hapus Terpilih"
              icon="pi pi-trash"
              class="p-button-danger p-button-sm"
              @click="deleteSelectedArticles"
            />
          </div>

          <!-- DataTable -->
          <DataTable
            v-model:selection="selectedArticles"
            :value="filteredArticles"
            dataKey="id"
            paginator
            rows="10"
            :rowsPerPageOptions="[5,10,20]"
            stripedRows
            removableSort
            sortMode="multiple"
            responsiveLayout="scroll"
            class="text-sm"
          >
            <!-- Checkbox Select -->
            <Column selectionMode="multiple" headerStyle="width: 3rem"/>

            <!-- Judul -->
            <Column field="title" header="Judul" sortable style="min-width: 200px"/>

            <!-- Kategori -->
            <Column header="Kategori" style="min-width: 120px">
              <template #body="{ data }">
                <div class="flex items-center gap-2">
                  <Tag class="w-4 h-4 text-blue-500"/>
                  <span>{{ data.category || 'Tidak ada' }}</span>
                </div>
              </template>
            </Column>

            <!-- Hits -->
            <Column header="Hits" sortable style="width: 80px">
              <template #body="{ data }">
                <div class="flex items-center gap-1">
                  <Eye class="w-4 h-4 text-blue-500"/>
                  <span>{{ data.hits }}</span>
                </div>
              </template>
            </Column>

            <!-- Tanggal Dibuat -->
            <Column header="Tanggal Dibuat" sortable style="width: 140px">
              <template #body="{ data }">
                {{ formatDate(data.created_at) }}
              </template>
            </Column>

            <!-- Aksi -->
            <Column header="Aksi" style="width: 120px">
              <template #body="{ data }">
                <Button icon="pi pi-trash" label="Hapus" class="p-button-danger p-button-sm" @click="deleteArticle(data.id)"/>
              </template>
            </Column>

          </DataTable>

        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
