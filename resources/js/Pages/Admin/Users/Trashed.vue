<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { Trash2, RotateCcw, Archive } from 'lucide-vue-next'
import Swal from 'sweetalert2'

const props = defineProps({
  users: Array
})

const search = ref('')
const selectedUsers = ref([]) // ✅ multiple select

// 🔹 Filtering pencarian
const filteredUsers = computed(() => {
  if (!search.value) return props.users
  return props.users.filter(user =>
    user.username.toLowerCase().includes(search.value.toLowerCase()) ||
    user.email.toLowerCase().includes(search.value.toLowerCase())
  )
})

// 🔹 Restore user tunggal
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
        }
      })
    }
  })
}

// 🔹 Hapus permanen tunggal
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
        }
      })
    }
  })
}

// 🔹 Bulk Restore
const bulkRestore = () => {
  if (selectedUsers.value.length === 0) {
    Swal.fire('Oops!', 'Pilih minimal satu user dulu.', 'warning')
    return
  }
  Swal.fire({
    title: `Restore ${selectedUsers.value.length} user?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, restore',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(route('admin.users.bulkRestore'), {
        ids: selectedUsers.value.map(u => u.id)
      }, {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire('Sukses!', 'User berhasil direstore.', 'success')
          selectedUsers.value = []
        }
      })
    }
  })
}

// 🔹 Bulk Delete Permanen
const bulkForceDelete = () => {
  if (selectedUsers.value.length === 0) {
    Swal.fire('Oops!', 'Pilih minimal satu user dulu.', 'warning')
    return
  }
  Swal.fire({
    title: `Hapus permanen ${selectedUsers.value.length} user?`,
    text: "User akan hilang selamanya!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus permanen',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('admin.users.bulkForceDelete'), {
        data: { ids: selectedUsers.value.map(u => u.id) },
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire('Terhapus!', 'User berhasil dihapus permanen.', 'success')
          selectedUsers.value = []
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
      <div
        class="w-full flex justify-between items-center bg-yellow-100 border border-yellow-200 rounded-lg px-4 py-3 shadow-sm"
      >
        <div class="flex items-center gap-2">
          <Archive class="h-6 w-6 text-yellow-600" />
          <h2 class="text-lg sm:text-xl font-semibold text-yellow-800 truncate">
            Trashed Users
          </h2>
        </div>

        <div class="flex items-center gap-2">
          <Link
            :href="route('admin.users')"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium rounded-lg shadow transition"
          >
            ✅ Users
          </Link>
        </div>
      </div>
    </template>

    <div class="bg-white shadow-xl sm:rounded-lg p-6 mt-6">
      <!-- Tombol Bulk -->
      <div class="flex gap-2 mb-4">
        <button
          @click="bulkRestore"
          class="px-4 py-2 bg-green-600 text-white rounded shadow hover:bg-green-700 disabled:opacity-50"
          :disabled="selectedUsers.length === 0"
        >
          Restore Terpilih ({{ selectedUsers.length }})
        </button>
        <button
          @click="bulkForceDelete"
          class="px-4 py-2 bg-red-600 text-white rounded shadow hover:bg-red-700 disabled:opacity-50"
          :disabled="selectedUsers.length === 0"
        >
          Hapus Permanen ({{ selectedUsers.length }})
        </button>
      </div>

      <!-- Search -->
      <div class="mb-4">
        <input
          v-model="search"
          type="text"
          placeholder="Cari user..."
          class="border rounded px-3 py-1 w-full sm:w-1/3"
        />
      </div>

      <!-- DataTable -->
      <DataTable
        v-model:selection="selectedUsers"
        :value="filteredUsers"
        dataKey="id"
        paginator
        rows="10"
        stripedRows
        removableSort
        sortMode="multiple"
        responsiveLayout="scroll"
        size="small"
        class="text-sm"
      >
        <!-- Checkbox Select All -->
        <Column selectionMode="multiple" headerStyle="width: 3rem" />

        <Column field="name" header="Nama" sortable style="width: 150px" />
        <Column field="email" header="Email" sortable style="width: 180px" />
        <Column field="username" header="Username" sortable style="width: 120px" />

        <!-- Aksi -->
        <Column header="Aksi" style="width: 180px">
          <template #body="{ data }">
            <div class="flex gap-1 flex-wrap">
              <Button
                icon="pi pi-refresh"
                size="small"
                text
                severity="success"
                @click="restore(data.id)"
              />
              <Button
                icon="pi pi-trash"
                severity="danger"
                size="small"
                text
                @click="forceDelete(data.id)"
              />
            </div>
          </template>
        </Column>
      </DataTable>

      <div v-if="!filteredUsers.length" class="text-center text-gray-500 py-6">
        Tidak ada user di trash.
      </div>
    </div>
  </AuthenticatedLayout>
</template>
