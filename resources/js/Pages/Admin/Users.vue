<script setup>
import { ref, computed } from 'vue'
import { Head, useForm, router, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import Modal from '@/Components/Modal.vue'
import Swal from 'sweetalert2'
import { Archive } from 'lucide-vue-next'

const props = defineProps({ users: Array })

const showModal = ref(false)
const editMode = ref(false)
const selectedUser = ref(null)
const search = ref('')

const form = useForm({
  name: '',
  email: '',
  username: '',
  roles: [],
  is_active: true,
  trusted_writer: false,
  password: '',
  password_confirmation: '',
})

// --- Modal handling ---
const openModal = (user = null) => {
  showModal.value = true
  editMode.value = !!user
  selectedUser.value = user

  if (user) {
    form.name = user.name
    form.email = user.email
    form.username = user.username
    form.roles = user.roles || []
    form.is_active = !!user.is_active
    form.trusted_writer = !!user.trusted_writer
    form.password = ''
    form.password_confirmation = ''
  } else {
    form.reset()
    form.roles = []
    form.is_active = true
    form.trusted_writer = false
  }
}

const closeModal = () => {
  form.reset()
  form.roles = []
  form.is_active = true
  form.trusted_writer = false
  showModal.value = false
}

// --- Save user (create / update) ---
const saveUser = () => {
  if (editMode.value) {
    form.put(route('admin.users.update', selectedUser.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'User berhasil diperbarui.',
          timer: 2000,
          showConfirmButton: false,
        })
      },
      onError: () => Swal.fire('Oops!', 'Terjadi kesalahan', 'error'),
    })
  } else {
    form.post(route('admin.users.store'), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'User berhasil ditambahkan.',
          timer: 2000,
          showConfirmButton: false,
        })
      },
      onError: () => Swal.fire('Oops!', 'Terjadi kesalahan', 'error'),
    })
  }
}

// --- Delete user pakai SweetAlert2 ---
const deleteUser = (id) => {
  Swal.fire({
    title: 'Yakin ingin menghapus?',
    text: "Data user akan masuk trash!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      form.delete(route('admin.users.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Terhapus!',
            text: 'User berhasil dihapus.',
            timer: 2000,
            showConfirmButton: false,
          })
        },
        onError: () => Swal.fire('Oops!', 'Gagal hapus user', 'error'),
      })
    }
  })
}

// --- Toggle aktif/nonaktif ---
const toggleStatus = (user) => {
  form.clearErrors()
  form.name = user.name
  form.email = user.email
  form.username = user.username
  form.roles = user.roles || []
  form.is_active = !user.is_active
  form.trusted_writer = user.trusted_writer

  form.put(route('admin.users.update', user.id), {
    preserveScroll: true,
    onSuccess: () => {
      user.is_active = !user.is_active
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Status user diperbarui',
        timer: 1500,
        showConfirmButton: false,
      })
    },
    onError: () => Swal.fire('Oops!', 'Gagal update status', 'error'),
  })
}

// --- Toggle trusted writer ---
const toggleTrusted = (user) => {
  form.clearErrors()
  form.name = user.name
  form.email = user.email
  form.username = user.username
  form.roles = user.roles || []
  form.is_active = user.is_active
  form.trusted_writer = !user.trusted_writer

  form.put(route('admin.users.update', user.id), {
    preserveScroll: true,
    onSuccess: () => {
      user.trusted_writer = !user.trusted_writer
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Status Trusted Writer diperbarui',
        timer: 1500,
        showConfirmButton: false,
      })
    },
    onError: () => Swal.fire('Oops!', 'Gagal update trusted writer', 'error'),
  })
}

// --- Password validation ---
const isPasswordLongEnough = computed(() => form.password.length >= 8)
const isPasswordStrong = computed(() =>
  /(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])/.test(form.password)
)

// --- Search filter ---
const filteredUsers = computed(() => {
  if (!search.value) return props.users
  return props.users.filter(user =>
    user.username.toLowerCase().includes(search.value.toLowerCase())
  )
})
</script>


<template>
  <Head title="Manajemen User" />
  <AuthenticatedLayout>
   <template #header>
      <!-- Wrapper full width -->
      <div
        class="w-full flex justify-between items-center bg-blue-100 border border-blue-200 rounded-lg px-4 py-3 shadow-sm"
      >
        <!-- Judul -->
        <div class="flex items-center gap-2">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 text-blue-600"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M5.121 17.804A13.937 13.937 0 0112 15c2.21 0 4.29.534 6.121 1.475M15 10a3 3 0 11-6 0 3 0 016 0z"
            />
          </svg>
          <h2 class="text-lg sm:text-xl font-semibold text-blue-800 truncate">
            Users Management
          </h2>
        </div>

        <!-- Tombol kanan -->
        <div class="flex items-center gap-2">
          <!-- Link ke Trashed -->
          <Link
            :href="route('admin.users.trashed')"
            class="inline-flex items-center gap-2 bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 text-sm font-medium rounded-lg shadow transition"
          >
            <Archive class="w-4 h-4" /> Trash
          </Link>

          <!-- Link ke Artikel Publik -->
          <Link
            :href="route('guest.welcome')"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium rounded-lg shadow transition"
          >
            📖 Lihat Artikel
          </Link>
        </div>
      </div>
    </template>


    <div class="py-12">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white shadow-xl sm:rounded-lg p-6">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2">
        <h3 class="text-lg font-bold text-gray-700">Daftar User</h3>
        <PrimaryButton @click="openModal()">Tambah User</PrimaryButton>
      </div>

      <!-- Input Pencarian -->
      <div class="mb-4">
        <input
          v-model="search"
          type="text"
          placeholder="Cari username..."
          class="border rounded px-3 py-1 w-full sm:w-1/3"
        />
      </div>

      <!-- Table Wrapper -->
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left min-w-[600px]">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2">Nama</th>
              <th class="px-4 py-2">Username</th>
              <th class="px-4 py-2">Email</th>
              <th class="px-4 py-2">Roles</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2">Trusted</th>
              <th class="px-4 py-2">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in filteredUsers" :key="user.id" class="border-t">
              <td class="px-4 py-2">{{ user.name }}</td>
              <td class="px-4 py-2">{{ user.username }}</td>
              <td class="px-4 py-2">{{ user.email }}</td>
              <td class="px-4 py-2 capitalize">{{ Array.isArray(user.roles) ? user.roles.join(', ') : '-' }}</td>
              <td class="px-4 py-2">
                <button
                  @click="toggleStatus(user)"
                  class="text-xs px-2 py-1 rounded-full font-semibold transition"
                  :class="user.is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200'"
                >
                  {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                </button>
              </td>
              <td class="px-4 py-2">
                <button
                  @click="toggleTrusted(user)"
                  class="text-xs px-2 py-1 rounded-full font-semibold transition"
                  :class="user.trusted_writer ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-200 text-gray-600 hover:bg-gray-300'"
                >
                  {{ user.trusted_writer ? 'Trusted' : 'Biasa' }}
                </button>
              </td>
              <td class="px-4 py-2">
                <div class="flex flex-wrap gap-2">
                  <PrimaryButton size="sm" @click="openModal(user)">Edit</PrimaryButton>
                  <DangerButton size="sm" @click="deleteUser(user.id)">Hapus</DangerButton>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>


    </div>
  </div>
</div>

<!-- Modal Form Tambah/Edit User -->

  <Modal :show="showModal" @close="closeModal">
  <div class="p-6 w-full max-w-lg mx-auto">
    <h2 class="text-lg sm:text-xl font-semibold mb-4 text-gray-800">
      {{ editMode ? 'Edit User' : 'Tambah User' }}
    </h2>

    <form @submit.prevent="saveUser" class="space-y-4">
      <!-- Nama -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Nama</label>
        <input v-model="form.name" type="text"
               class="w-full mt-1 rounded-md shadow-sm border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
      </div>

      <!-- Email -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input v-model="form.email" type="email"
               class="w-full mt-1 rounded-md shadow-sm border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
      </div>

      <!-- Username -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Username</label>
        <input v-model="form.username" type="text"
               class="w-full mt-1 rounded-md shadow-sm border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
      </div>

<!-- Role -->
<div>
  <label class="block text-sm font-medium text-gray-700 mb-2">Roles</label>
  <div class="flex flex-wrap gap-3">
    <label v-for="role in ['super-admin', 'admin', 'operator']" :key="role"
           class="flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer transition
                  hover:bg-blue-50"
           :class="form.roles.includes(role) ? 'bg-blue-100 border-blue-400' : 'bg-white border-gray-300'">
      <input type="checkbox" :value="role" v-model="form.roles" class="hidden" />
      <span class="text-sm font-medium capitalize">{{ role }}</span>
    </label>
  </div>
  <p class="text-xs text-gray-500 mt-1">✓ Bisa pilih lebih dari satu role</p>
</div>



      <!-- Status -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Status</label>
        <select v-model="form.is_active"
                class="w-full mt-1 rounded-md shadow-sm border-gray-300 focus:ring-blue-500 focus:border-blue-500">
          <option :value="true">Aktif</option>
          <option :value="false">Nonaktif</option>
        </select>
      </div>

      <!-- Trusted Writer -->
      <div>
        <label class="inline-flex items-center">
          <input type="checkbox" v-model="form.trusted_writer"
                 class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500" />
          <span class="ml-2 text-sm text-gray-700">Trusted Writer</span>
        </label>
      </div>

      <!-- Password -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Password</label>
          <input v-model="form.password" type="password"
                 class="w-full mt-1 rounded-md shadow-sm border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
          <input v-model="form.password_confirmation" type="password"
                 class="w-full mt-1 rounded-md shadow-sm border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
        </div>
      </div>

      <!-- Validasi Password -->
      <div
        class="mt-2 rounded-md px-4 py-2 text-sm"
        :class="{
          'bg-red-100 border border-red-300': !isPasswordLongEnough,
          'bg-yellow-100 border border-yellow-300': isPasswordLongEnough && !isPasswordStrong,
          'bg-blue-100 border border-blue-300': isPasswordLongEnough && isPasswordStrong && form.password !== form.password_confirmation,
          'bg-green-100 border border-green-300': isPasswordLongEnough && isPasswordStrong && form.password === form.password_confirmation
        }"
      >
        <ul class="list-disc list-inside">
          <li v-if="!isPasswordLongEnough" class="text-red-600">Minimal 8 karakter</li>
          <li v-else-if="!isPasswordStrong" class="text-yellow-700">Gunakan huruf besar, kecil, angka, dan simbol</li>
          <li v-else-if="form.password !== form.password_confirmation" class="text-blue-700">Konfirmasi belum cocok</li>
          <li v-else class="text-green-700 font-medium">✓ Password valid!</li>
        </ul>
      </div>

      <!-- Buttons -->
      <div class="mt-6 flex flex-wrap gap-2 justify-end">
        <DangerButton type="button" @click="closeModal">Batal</DangerButton>
        <PrimaryButton type="submit" :disabled="form.processing">{{ editMode ? 'Simpan' : 'Tambah' }}</PrimaryButton>
      </div>
    </form>
  </div>
</Modal>


  </AuthenticatedLayout>
</template>
