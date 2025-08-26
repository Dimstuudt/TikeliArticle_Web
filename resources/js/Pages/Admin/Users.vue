<script setup>
import { ref, computed } from 'vue'
import { Head, useForm, router, Link} from '@inertiajs/vue3' // ✅ tambahin router
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({ users: Array })

const showModal = ref(false)
const editMode = ref(false)
const selectedUser = ref(null)
const search = ref('')

const form = useForm({
  name: '',
  email: '',
  username: '',
  role: 'operator',
  is_active: true,
  trusted_writer: false, // ✅ untuk badge
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
    form.role = user.role
    form.is_active = !!user.is_active
    form.trusted_writer = !!user.trusted_writer
    form.password = ''
    form.password_confirmation = ''
  } else {
    form.reset()
    form.role = 'operator'
    form.is_active = true
    form.trusted_writer = false
  }
}

const closeModal = () => {
  form.reset()
  form.role = 'operator'
  form.is_active = true
  form.trusted_writer = false
  showModal.value = false
}

// --- Save user (create / update) ---
const saveUser = () => {
  if (editMode.value) {
    form.put(route('admin.users.update', selectedUser.value.id), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
      onError: () => console.log(form.errors),
    })
  } else {
    form.post(route('admin.users.store'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
      onError: () => console.log(form.errors),
    })
  }
}

// --- Toggle aktif/nonaktif ---
const toggleStatus = (user) => {
  form.clearErrors()
  form.name = user.name
  form.email = user.email
  form.username = user.username
  form.role = user.role
  form.is_active = !user.is_active
  form.trusted_writer = user.trusted_writer

  form.put(route('admin.users.update', user.id), {
    preserveScroll: true,
    onSuccess: () => {
      user.is_active = !user.is_active // ✅ update UI langsung
    },
    onError: () => alert('Gagal update status'),
  })
}

// --- Toggle trusted writer ---
const toggleTrusted = (user) => {
  form.clearErrors()
  form.name = user.name
  form.email = user.email
  form.username = user.username
  form.role = user.role
  form.is_active = user.is_active
  form.trusted_writer = !user.trusted_writer

  form.put(route('admin.users.update', user.id), {
    preserveScroll: true,
    onSuccess: () => {
      user.trusted_writer = !user.trusted_writer // ✅ update UI langsung
    },
    onError: () => alert('Gagal update trusted writer'),
  })
}

// --- Delete user ---
const deleteUser = (id) => {
  if (confirm('Yakin hapus user ini?')) {
    form.delete(route('admin.users.destroy', id), {
      preserveScroll: true,
    })
  }
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
      <div class="flex items-center justify-between bg-blue-100 border border-blue-200 rounded-lg px-4 py-3 shadow-sm">
        <div class="flex items-center gap-2">
          <h2 class="text-lg sm:text-xl font-semibold text-blue-800"> Manajemen User</h2>
        </div>
        <Link
          :href="route('guest.welcome')"
          class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium rounded-lg shadow transition"
        >
          📖 Lihat Artikel
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-700">Daftar User</h3>
            <PrimaryButton @click="openModal()">Tambah User</PrimaryButton>
          </div>

          <!-- Input Pencarian -->
          <input
            v-model="search"
            type="text"
            placeholder="Cari username..."
            class="border rounded px-3 py-1 mb-4 w-1/3"
          />

          <table class="w-full text-sm text-left">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">Username</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Role</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Trusted</th> <!-- ✅ baru -->
                <th class="px-4 py-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in filteredUsers" :key="user.id" class="border-t">
                <td class="px-4 py-2">{{ user.name }}</td>
                <td class="px-4 py-2">{{ user.username }}</td>
                <td class="px-4 py-2">{{ user.email }}</td>
                <td class="px-4 py-2 capitalize">{{ user.role }}</td>
                <td class="px-4 py-2">
                  <button
                    @click="toggleStatus(user)"
                    class="text-xs px-3 py-1 rounded-full font-semibold transition"
                    :class="user.is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200'"
                  >
                    {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                  </button>

                </td>
              <td class="px-4 py-2">
  <button
  @click="toggleTrusted(user)"
  class="text-xs px-3 py-1 rounded-full font-semibold transition"
  :class="user.trusted_writer ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-200 text-gray-600 hover:bg-gray-300'"
>
  {{ user.trusted_writer ? 'Trusted' : 'Biasa' }}
</button>

</td>
                <td class="px-4 py-2 space-x-2">
                  <PrimaryButton @click="openModal(user)">Edit</PrimaryButton>
                  <DangerButton @click="deleteUser(user.id)">Hapus</DangerButton>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Form -->
    <Modal :show="showModal" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-semibold mb-4">
          {{ editMode ? 'Edit User' : 'Tambah User' }}
        </h2>

        <form @submit.prevent="saveUser" class="space-y-4">
          <div>
            <label class="block text-sm font-medium">Nama</label>
            <input v-model="form.name" class="w-full mt-1 rounded-md shadow-sm" type="text" />
          </div>

          <div>
            <label class="block text-sm font-medium">Email</label>
            <input v-model="form.email" class="w-full mt-1 rounded-md shadow-sm" type="email" />
          </div>

          <div>
            <label class="block text-sm font-medium">Username</label>
            <input v-model="form.username" class="w-full mt-1 rounded-md shadow-sm" type="text" />
          </div>

          <div>
            <label class="block text-sm font-medium">Role</label>
            <select v-model="form.role" class="w-full mt-1 rounded-md shadow-sm">
              <option value="admin">Admin</option>
              <option value="operator">Operator</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium">Status</label>
            <select v-model="form.is_active" class="w-full mt-1 rounded-md shadow-sm">
              <option :value="true">Aktif</option>
              <option :value="false">Nonaktif</option>
            </select>
          </div>

          <!-- ✅ Checkbox Trusted Writer -->
          <div>
            <label class="inline-flex items-center">
              <input type="checkbox" v-model="form.trusted_writer"
                     class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500" />
              <span class="ml-2 text-sm text-gray-700">Trusted Writer</span>
            </label>
          </div>

          <div>
            <label class="block text-sm font-medium">Password</label>
            <input v-model="form.password" class="w-full mt-1 rounded-md shadow-sm" type="password" />
          </div>

          <div>
            <label class="block text-sm font-medium">Konfirmasi Password</label>
            <input v-model="form.password_confirmation" class="w-full mt-1 rounded-md shadow-sm" type="password" />
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
            <ul>
              <li v-if="!isPasswordLongEnough" class="text-red-600">Minimal 8 karakter</li>
              <li v-else-if="!isPasswordStrong" class="text-yellow-700">Gunakan huruf besar, kecil, angka, dan simbol</li>
              <li v-else-if="form.password !== form.password_confirmation" class="text-green-700">Konfirmasi belum cocok</li>
              <li v-else class="text-green-700 font-medium">✓ Password valid!</li>
            </ul>
          </div>

          <div class="mt-6 flex justify-end space-x-2">
            <DangerButton type="button" @click="closeModal">Batal</DangerButton>
            <PrimaryButton type="submit" :disabled="form.processing">
              {{ editMode ? 'Simpan' : 'Tambah' }}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>
